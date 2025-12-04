<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\Hotel;
use App\Models\Car;
use App\Models\Flight;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * البحث الشامل في جميع الأنواع
     *
     * @param string $query نص البحث
     * @param array $filters الفلاتر
     * @param array $types أنواع البحث (tours, hotels, cars, flights)
     * @param int $perPage عدد النتائج في الصفحة
     * @return array
     */
    public function search(string $query, array $filters = [], array $types = ['tours'], int $perPage = 20): array
    {
        $results = [];

        foreach ($types as $type) {
            $results[$type] = match ($type) {
                'tours' => $this->searchTours($query, $filters, $perPage),
                'hotels' => $this->searchHotels($query, $filters, $perPage),
                'cars' => $this->searchCars($query, $filters, $perPage),
                'flights' => $this->searchFlights($query, $filters, $perPage),
                default => null,
            };
        }

        return $results;
    }

    /**
     * البحث في الجولات
     */
    public function searchTours(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = Tour::query()
            ->with(['category', 'media'])
            ->where('status', 'active');

        // البحث النصي في الحقول المترجمة
        if (! empty($query)) {
            $searchQuery->whereHas('translations', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%")
                    ->orWhere('highlights', 'like', "%{$query}%");
            });
        }

        // تطبيق الفلاتر
        $this->applyTourFilters($searchQuery, $filters);

        // البحث الجغرافي
        if (isset($filters['location_lat']) && isset($filters['location_lng'])) {
            $radius = $filters['radius'] ?? 50; // km
            $searchQuery->byLocation($filters['location_lat'], $filters['location_lng'], $radius);
        }

        // الترتيب
        $searchQuery->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc');

        return $searchQuery->paginate($perPage);
    }

    /**
     * البحث في الفنادق
     */
    public function searchHotels(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = Hotel::query()
            ->with(['location']);

        // البحث النصي
        if (! empty($query)) {
            $searchQuery->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        }

        // تطبيق الفلاتر
        $this->applyHotelFilters($searchQuery, $filters);

        // البحث الجغرافي
        if (isset($filters['location_lat']) && isset($filters['location_lng'])) {
            $radius = $filters['radius'] ?? 50;
            $this->applyLocationFilter($searchQuery, $filters['location_lat'], $filters['location_lng'], $radius, 'location');
        }

        // الترتيب (Hotel recommended is array, so we can't order by it directly)
        $searchQuery->orderBy('stars', 'desc')
            ->orderBy('name', 'asc');

        return $searchQuery->paginate($perPage);
    }

    /**
     * البحث في السيارات
     */
    public function searchCars(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = Car::query()
            ->with(['brand', 'pickupLocation', 'dropoffLocation']);

        // البحث النصي (Car model uses model, make, category)
        if (! empty($query)) {
            $searchQuery->where(function ($q) use ($query) {
                $q->where('model', 'like', "%{$query}%")
                    ->orWhere('make', 'like', "%{$query}%")
                    ->orWhere('category', 'like', "%{$query}%");
            });
        }

        // تطبيق الفلاتر
        $this->applyCarFilters($searchQuery, $filters);

        // البحث الجغرافي
        if (isset($filters['location_lat']) && isset($filters['location_lng'])) {
            $radius = $filters['radius'] ?? 50;
            $searchQuery->where(function ($q) use ($filters, $radius) {
                $q->whereHas('pickupLocation', function ($subQ) use ($filters, $radius) {
                    $subQ->whereRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                        [$filters['location_lat'], $filters['location_lng'], $filters['location_lat'], $radius]
                    );
                })->orWhereHas('dropoffLocation', function ($subQ) use ($filters, $radius) {
                    $subQ->whereRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                        [$filters['location_lat'], $filters['location_lng'], $filters['location_lat'], $radius]
                    );
                });
            });
        }

        // الترتيب (TODO: إضافة price_per_day و recommended لجدول cars)
        $searchQuery->orderBy('created_at', 'desc');

        return $searchQuery->paginate($perPage);
    }

    /**
     * البحث في الرحلات الجوية
     */
    public function searchFlights(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = Flight::query()
            ->with(['carrier', 'aircraft', 'origin', 'destination']);

        // البحث النصي
        if (! empty($query)) {

            $searchQuery->where(function ($q) use ($query) {
                $q->where('flight_number', 'like', "%{$query}%")
                    ->orWhereHas('origin', function ($subQ) use ($query) {
                        $subQ->where('city', 'like', "%{$query}%")
                            ->orWhere('country', 'like', "%{$query}%");
                    })
                    ->orWhereHas('destination', function ($subQ) use ($query) {
                        $subQ->where('city', 'like', "%{$query}%")
                            ->orWhere('country', 'like', "%{$query}%");
                    });
            });
         }

        // تطبيق الفلاتر
        $this->applyFlightFilters($searchQuery, $filters);

        // الترتيب
        $searchQuery->orderBy('departure_time', 'asc');

        return $searchQuery->paginate($perPage);
    }


    /**
     * تطبيق فلاتر الجولات
     */
    protected function applyTourFilters($query, array $filters): void
    {
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['price_min'])) {
            $query->where('adult_price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('adult_price', '<=', $filters['price_max']);
        }

        if (isset($filters['rating_min'])) {
            $query->where('rating', '>=', $filters['rating_min']);
        }

        if (isset($filters['date'])) {
            $query->whereHas('availability', function ($q) use ($filters) {
                $q->where('date', $filters['date'])
                    ->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots');
            });
        }

        if (isset($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (isset($filters['languages']) && is_array($filters['languages'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['languages'] as $language) {
                    $q->orWhereJsonContains('languages', $language);
                }
            });
        }

        if (isset($filters['tags']) && is_array($filters['tags'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['tags'] as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        if (isset($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }
    }

    /**
     * تطبيق فلاتر الفنادق
     */
    protected function applyHotelFilters($query, array $filters): void
    {
        if (isset($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (isset($filters['stars'])) {
            if (is_array($filters['stars'])) {
                $query->whereIn('stars', $filters['stars']);
            } else {
                $query->where('stars', '>=', $filters['stars']);
            }
        }

        if (isset($filters['price_min'])) {
            // TODO: إضافة جدول hotel_prices
            // $query->where('price_per_night', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            // TODO: إضافة جدول hotel_prices
            // $query->where('price_per_night', '<=', $filters['price_max']);
        }

        if (isset($filters['check_in']) && isset($filters['check_out'])) {
            // TODO: التحقق من التوفر
        }

        // Hotel recommended is array, so we check if it contains the value
        if (isset($filters['recommended'])) {
            // TODO: تحديد منطق recommended للفنادق
            // $query->whereJsonContains('recommended', $filters['recommended']);
        }
    }

    /**
     * تطبيق فلاتر السيارات
     */
    protected function applyCarFilters($query, array $filters): void
    {
        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        // TODO: إضافة price_per_day لجدول cars
        // if (isset($filters['price_min'])) {
        //     $query->where('price_per_day', '>=', $filters['price_min']);
        // }
        //
        // if (isset($filters['price_max'])) {
        //     $query->where('price_per_day', '<=', $filters['price_max']);
        // }

        if (isset($filters['pickup_date']) && isset($filters['dropoff_date'])) {
            // TODO: التحقق من التوفر
        }

        // TODO: إضافة recommended لجدول cars
        // if (isset($filters['recommended'])) {
        //     $query->where('recommended', $filters['recommended']);
        // }
    }

    /**
     * تطبيق فلاتر الرحلات الجوية
     */
    protected function applyFlightFilters($query, array $filters): void
    {
        if (isset($filters['origin_id'])) {
            $query->where('origin_id', $filters['origin_id']);
        }

        if (isset($filters['destination_id'])) {
            $query->where('destination_id', $filters['destination_id']);
        }

        if (isset($filters['departure_date'])) {
            $query->whereDate('departure_time', $filters['departure_date']);
        }

        if (isset($filters['return_date'])) {
            $query->whereDate('arrival_time', $filters['return_date']);
        }

        if (isset($filters['carrier_id'])) {
            $query->where('carrier_id', $filters['carrier_id']);
        }

        if (isset($filters['class_id'])) {
            $query->whereHas('classes', function ($q) use ($filters) {
                $q->where('flight_classes.id', $filters['class_id']);
            });
        }
    }

    /**
     * تطبيق فلتر الموقع الجغرافي (Haversine formula)
     */
    protected function applyLocationFilter($query, float $lat, float $lng, float $radius = 50, string $locationColumn = 'location'): void
    {
        // إذا كان الجدول يحتوي على location_id (relation)
        if ($locationColumn === 'location') {
            $query->whereHas('location', function ($q) use ($lat, $lng, $radius) {
                $q->whereRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                    [$lat, $lng, $lat, $radius]
                );
            });
        } else {
            // إذا كانت الإحداثيات مباشرة في الجدول (Location model uses latitude/longitude)
            $query->whereRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                [$lat, $lng, $lat, $radius]
            );
        }
    }

    /**
     * البحث السريع (Quick Search) - البحث في جميع الأنواع مع ترتيب حسب الصلة
     */
    public function quickSearch(string $query, ?float $lat = null, ?float $lng = null, float $radius = 50, int $limit = 10): array
    {
        $results = [];

        // البحث في الجولات
        $tours = Tour::query()
            ->with(['category'])
            ->where('status', 'active')
            ->whereHas('translations', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%");
            });

        if ($lat && $lng) {
            $tours->byLocation($lat, $lng, $radius);
        }

        $results['tours'] = $tours->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();

        // البحث في الفنادق
        $hotels = Hotel::query()
            ->with(['location'])
            ->where('name', 'like', "%{$query}%");

        if ($lat && $lng) {
            $hotels->whereHas('location', function ($q) use ($lat, $lng, $radius) {
                $q->whereRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                    [$lat, $lng, $lat, $radius]
                );
            });
        }

        $results['hotels'] = $hotels->orderBy('stars', 'desc')
            ->orderBy('name', 'asc')
            ->limit($limit)
            ->get();


        // البحث في السيارات
        $cars = Car::query()
            ->with(['brand', 'pickupLocation', 'dropoffLocation'])
            ->where(function ($q) use ($query) {
                $q->where('model', 'like', "%{$query}%")
                    ->orWhere('make', 'like', "%{$query}%")
                    ->orWhere('category', 'like', "%{$query}%");
            });

        if ($lat && $lng) {
            $cars->where(function ($q) use ($lat, $lng, $radius) {
                $q->whereHas('pickupLocation', function ($subQ) use ($lat, $lng, $radius) {
                    $subQ->whereRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                        [$lat, $lng, $lat, $radius]
                    );
                })->orWhereHas('dropoffLocation', function ($subQ) use ($lat, $lng, $radius) {
                    $subQ->whereRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                        [$lat, $lng, $lat, $radius]
                    );
                });
            });
        }

        $results['cars'] = $cars->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // البحث في الرحلات الجوية
        $flights = Flight::query()
            ->with(['carrier', 'aircraft', 'origin', 'destination'])
            ->where(function ($q) use ($query) {
                $q->where('flight_number', 'like', "%{$query}%")
                    ->orWhereHas('origin', function ($subQ) use ($query) {
                        $subQ->where('city', 'like', "%{$query}%")
                            ->orWhere('country', 'like', "%{$query}%");
                    })
                    ->orWhereHas('destination', function ($subQ) use ($query) {
                        $subQ->where('city', 'like', "%{$query}%")
                            ->orWhere('country', 'like', "%{$query}%");
                    });
            });

        $results['flights'] = $flights->orderBy('departure_time', 'asc')
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * البحث بالقرب من موقع معين (Nearby Search)
     */
    public function searchNearby(float $lat, float $lng, array $types = ['tours', 'hotels'], float $radius = 10, int $limit = 20): array
    {
        $results = [];

        foreach ($types as $type) {
            $results[$type] = match ($type) {
                'tours' => Tour::query()
                    ->with(['category'])
                    ->where('status', 'active')
                    ->byLocation($lat, $lng, $radius)
                    ->orderBy('rating', 'desc')
                    ->limit($limit)
                    ->get(),

                'hotels' => Hotel::query()
                    ->with(['location'])
                    ->whereHas('location', function ($q) use ($lat, $lng, $radius) {
                        $q->whereRaw(
                            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                            [$lat, $lng, $lat, $radius]
                        );
                    })
                    ->orderBy('stars', 'desc')
                    ->orderBy('name', 'asc')
                    ->limit($limit)
                    ->get(),


                'cars' => Car::query()
                    ->with(['brand', 'pickupLocation', 'dropoffLocation'])
                    ->where(function ($q) use ($lat, $lng, $radius) {
                        $q->whereHas('pickupLocation', function ($subQ) use ($lat, $lng, $radius) {
                            $subQ->whereRaw(
                                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                                [$lat, $lng, $lat, $radius]
                            );
                        })->orWhereHas('dropoffLocation', function ($subQ) use ($lat, $lng, $radius) {
                            $subQ->whereRaw(
                                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                                [$lat, $lng, $lat, $radius]
                            );
                        });
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get(),

                'flights' => Flight::query()
                    ->with(['carrier', 'aircraft', 'origin', 'destination'])
                    ->where(function ($q) use ($lat, $lng, $radius) {
                        $q->whereHas('origin', function ($subQ) use ($lat, $lng, $radius) {
                            $subQ->whereRaw(
                                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                                [$lat, $lng, $lat, $radius]
                            );
                        })->orWhereHas('destination', function ($subQ) use ($lat, $lng, $radius) {
                            $subQ->whereRaw(
                                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                                [$lat, $lng, $lat, $radius]
                            );
                        });
                    })
                    ->orderBy('departure_time', 'asc')
                    ->limit($limit)
                    ->get(),
 
                default => collect(),
            };
        }

        return $results;
    }
}

