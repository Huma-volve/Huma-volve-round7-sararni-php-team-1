<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CompareRequest;
use App\Http\Resources\Api\V1\Compare\CarCompareResource;
use App\Http\Resources\Api\V1\Compare\FlightCompareResource;
use App\Http\Resources\Api\V1\Compare\HotelCompareResource;
use App\Http\Resources\Api\V1\Compare\TourCompareResource;
use App\Models\Car;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Tour;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function __construct(
        protected SearchService $searchService
    ) {}

    public function compare(CompareRequest $request): JsonResponse
    {
        $category = $request->category;
        $itemIds = $request->item_ids;
        $date = $request->date;

        $items = match ($category) {
            'tour' => $this->getTours($itemIds, $date),
            'flight' => $this->getFlights($itemIds, $date),
            'car' => $this->getCars($itemIds, $date),
            'hotel' => $this->getHotels($itemIds, $date),
            default => collect(),
        };

        // Sort items by the order of item_ids in request
        $items = $items->sortBy(function ($item) use ($itemIds) {
            return array_search($item->id, $itemIds);
        })->values();

        // Pass date to resource for availability checking
        $request->merge(['date' => $date]);

        $resourceClass = match ($category) {
            'tour' => TourCompareResource::class,
            'flight' => FlightCompareResource::class,
            'car' => CarCompareResource::class,
            'hotel' => HotelCompareResource::class,
            default => null,
        };

        if (!$resourceClass) {
            return $this->errorResponse('Invalid category', 400);
        }

        return $this->successResponse(
            $resourceClass::collection($items),
            ucfirst($category).'s compared successfully'
        );
    }

    private function getTours(array $itemIds, ?string $date)
    {
        $query = Tour::with([
            'category',
            'availability' => function ($query) use ($date) {
                if ($date) {
                    $query->where('date', $date);
                }
                $query->where('is_active', true);
            },
            'itineraries' => function ($query) {
                $query->orderBy('day_number')->orderBy('sort_order')->limit(1);
            },
            'media',
        ])
            ->whereIn('id', $itemIds)
            ->active();

        return $query->get();
    }

    private function getFlights(array $itemIds, ?string $date)
    {
        $query = Flight::with([
            'carrier',
            'aircraft',
            'flightClasses.class',
            'flightLegs.originAirport',
            'flightLegs.destinationAirport',
        ])
            ->whereIn('id', $itemIds);

        if ($date) {
            $query->whereHas('flightLegs', function ($q) use ($date) {
                $q->whereDate('departure_time', $date);
            });
        }

        return $query->get();
    }

    private function getCars(array $itemIds, ?string $date)
    {
        $query = Car::with([
            'brand',
            'pickupLocation',
            'dropoffLocation',
            'priceTiers',
        ])
            ->whereIn('id', $itemIds);

        return $query->get();
    }

    private function getHotels(array $itemIds, ?string $date)
    {
        $query = Hotel::with([
            'location',
        ])
            ->whereIn('id', $itemIds);

        return $query->get();
    }

    /**
     * Search and return results in compare format
     * This endpoint searches for items and returns them in compare format
     * Useful for the compare page where user searches first, then compares
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'category' => ['required', 'string', 'in:tour,flight,car,hotel'],
            'q' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date', 'after_or_equal:today'],
            'location' => ['nullable', 'string'], // City name like "Paris"
            'location_lat' => ['nullable', 'numeric'],
            'location_lng' => ['nullable', 'numeric'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $category = $request->category;
        $searchQuery = $request->q ?? '';
        $date = $request->date;
        $location = $request->location;
        $locationLat = $request->location_lat;
        $locationLng = $request->location_lng;
        $limit = $request->input('limit', 20);

        $items = match ($category) {
            'tour' => $this->searchTours($searchQuery, $location, $locationLat, $locationLng, $date, $limit),
            'flight' => $this->searchFlights($searchQuery, $location, $locationLat, $locationLng, $date, $limit),
            'car' => $this->searchCars($searchQuery, $location, $locationLat, $locationLng, $date, $limit),
            'hotel' => $this->searchHotels($searchQuery, $location, $locationLat, $locationLng, $date, $limit),
            default => collect(),
        };

        // Pass date to resource for availability checking
        $request->merge(['date' => $date]);

        $resourceClass = match ($category) {
            'tour' => TourCompareResource::class,
            'flight' => FlightCompareResource::class,
            'car' => CarCompareResource::class,
            'hotel' => HotelCompareResource::class,
            default => null,
        };

        if (!$resourceClass) {
            return $this->errorResponse('Invalid category', 400);
        }

        return $this->successResponse(
            $resourceClass::collection($items),
            'Search results for comparison'
        );
    }

    private function searchTours(?string $query, ?string $location, ?float $lat, ?float $lng, ?string $date, int $limit)
    {
        $searchQuery = Tour::with([
            'category',
            'availability' => function ($q) use ($date) {
                if ($date) {
                    $q->where('date', $date);
                }
                $q->where('is_active', true);
            },
            'itineraries' => function ($q) {
                $q->orderBy('day_number')->orderBy('sort_order')->limit(1);
            },
            'media',
        ])
            ->active();

        // Search by query text
        if ($query) {
            $searchQuery->whereHas('translations', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        // Search by location name (city)
        if ($location) {
            $searchQuery->whereHas('translations', function ($q) use ($location) {
                $q->where('meeting_point', 'like', "%{$location}%");
            });
        }

        // Search by coordinates
        if ($lat && $lng) {
            $searchQuery->byLocation($lat, $lng, 50);
        }

        // Filter by date availability
        if ($date) {
            $searchQuery->available($date);
        }

        return $searchQuery->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();
    }

    private function searchFlights(?string $query, ?string $location, ?float $lat, ?float $lng, ?string $date, int $limit)
    {
        $searchQuery = Flight::with([
            'carrier',
            'aircraft',
            'flightClasses.class',
            'flightLegs.originAirport',
            'flightLegs.destinationAirport',
        ]);

        // Search by flight number
        if ($query) {
            $searchQuery->where('flight_number', 'like', "%{$query}%");
        }

        // Search by location (origin or destination)
        if ($location) {
            $searchQuery->whereHas('flightLegs.originAirport', function ($q) use ($location) {
                $q->whereRaw('LOWER(city) LIKE ?', ["%{$location}%"])
                    ->orWhereRaw('LOWER(country) LIKE ?', ["%{$location}%"]);
            })->orWhereHas('flightLegs.destinationAirport', function ($q) use ($location) {
                $q->whereRaw('LOWER(city) LIKE ?', ["%{$location}%"])
                    ->orWhereRaw('LOWER(country) LIKE ?', ["%{$location}%"]);
            });
        }

        // Filter by date
        if ($date) {
            $searchQuery->whereHas('flightLegs', function ($q) use ($date) {
                $q->whereDate('departure_time', $date);
            });
        }

        return $searchQuery->orderBy('departure_time', 'asc')
            ->limit($limit)
            ->get();
    }

    private function searchCars(?string $query, ?string $location, ?float $lat, ?float $lng, ?string $date, int $limit)
    {
        $searchQuery = Car::with([
            'brand',
            'pickupLocation',
            'dropoffLocation',
            'priceTiers',
        ]);

        // Search by model, make, or category
        if ($query) {
            $searchQuery->where(function ($q) use ($query) {
                $q->where('model', 'like', "%{$query}%")
                    ->orWhere('make', 'like', "%{$query}%")
                    ->orWhere('category', 'like', "%{$query}%");
            });
        }

        // Search by location
        if ($location) {
            $searchQuery->whereHas('pickupLocation', function ($q) use ($location) {
                $q->where('name', 'like', "%{$location}%");
            })->orWhereHas('dropoffLocation', function ($q) use ($location) {
                $q->where('name', 'like', "%{$location}%");
            });
        }

        return $searchQuery->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    private function searchHotels(?string $query, ?string $location, ?float $lat, ?float $lng, ?string $date, int $limit)
    {
        $searchQuery = Hotel::with([
            'location',
        ]);

        // Search by name
        if ($query) {
            $searchQuery->where('name', 'like', "%{$query}%");
        }

        // Search by location
        if ($location) {
            $searchQuery->whereHas('location', function ($q) use ($location) {
                $q->where('name', 'like', "%{$location}%");
            });
        }

        return $searchQuery->orderBy('stars', 'desc')
            ->orderBy('name', 'asc')
            ->limit($limit)
            ->get();
    }
}

