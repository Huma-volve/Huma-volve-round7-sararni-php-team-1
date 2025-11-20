<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

use App\Http\Resources\Api\V1\FlightResource;
use App\Http\Resources\Api\V1\FlightDetailResource;

class FlightController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            $flights = Flight::with(['carrier', 'aircraft', 'flightClasses.class'])->latest()->get();

            return response()->json([
                'success' => true,
                'data' => FlightResource::collection($flights),
                'message' => 'show all data successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'error in getting flights'], 500);
        }
    }


    public function show($id): JsonResponse
    {
        try {
            $flight = Flight::with([
                'carrier',
                'aircraft',
                'flightClasses.class',
                'flightLegs.originAirport',
                'flightLegs.destinationAirport',
                'flightSeats.class'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new FlightDetailResource($flight),
                'message' => 'successfully show flight details'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Flight not found'], 404);
        }
    }


    public function search(Request $request): JsonResponse
    {
        try {
            $query = Flight::with([
                'carrier',
                'aircraft',
                'flightClasses.class',
                'flightLegs.originAirport',
                'flightLegs.destinationAirport'
            ]);


            if (!$request->anyFilled(['origin', 'destination', 'date', 'flight_number', 'carrier', 'class', 'passengers', 'price'])) {
                $flights = $query->latest()->get();

                return response()->json([
                    'success' => true,
                    'data' => $flights,
                    'message' => 'successful search results '
                ]);
            }


            $this->applyUniversalSearch($query, $request);

            $flights = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'search_query' => $request->all(),
                    'results' => $flights,
                    'count' => $flights->count()
                ],
                'message' => 'search done successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'error : ' . $e->getMessage()], 500);
        }
    }


    private function applyUniversalSearch($query, Request $request)
    {

        if ($request->filled('origin')) {
            $searchTerm = strtolower($request->origin);
            $query->whereHas('flightLegs.originAirport', function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(city) LIKE ?', ["%$searchTerm%"])
                    ->orWhereRaw('LOWER(country) LIKE ?', ["%$searchTerm%"])
                    ->orWhereRaw('LOWER(airport_name) LIKE ?', ["%$searchTerm%"])
                    ->orWhereRaw('LOWER(airport_code) LIKE ?', ["%$searchTerm%"]);
            });
        }


        if ($request->filled('destination')) {
            $searchTerm = strtolower($request->destination);
            $query->whereHas('flightLegs.destinationAirport', function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(city) LIKE ?', ["%$searchTerm%"])
                    ->orWhereRaw('LOWER(country) LIKE ?', ["%$searchTerm%"])
                    ->orWhereRaw('LOWER(airport_name) LIKE ?', ["%$searchTerm%"])
                    ->orWhereRaw('LOWER(airport_code) LIKE ?', ["%$searchTerm%"]);
            });
        }


        if ($request->filled('date') || $request->filled('departure_date')) {
            $date = $request->date ?? $request->departure_date;
            $query->whereHas('flightLegs', function ($q) use ($date) {
                $q->whereDate('departure_time', $date);
            });
        }

        if ($request->filled('return_date')) {
            $query->whereHas('flightLegs', function ($q) use ($request) {
                $q->whereDate('departure_time', $request->return_date);
            });
        }


        if ($request->filled('flight_number')) {
            $searchTerm = strtolower($request->flight_number);
            $query->whereRaw('LOWER(flight_number) LIKE ?', ["%$searchTerm%"]);
        }


        if ($request->filled('carrier')) {
            $searchTerm = strtolower($request->carrier);
            $query->whereHas('carrier', function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(carrier_name) LIKE ?', ["%$searchTerm%"])
                    ->orWhereRaw('LOWER(code) LIKE ?', ["%$searchTerm%"]);
            });
        }


        if ($request->filled('class')) {
            $searchTerm = strtolower($request->class);
            $query->whereHas('flightClasses.class', function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(class_name) LIKE ?', ["%$searchTerm%"]);
            });
        }


        if ($request->filled('passengers')) {
            $query->whereHas('flightClasses', function ($q) use ($request) {
                $q->where('seats_available', '>=', $request->passengers);
            });
        }


        if ($request->filled('price') || $request->filled('max_price')) {
            $price = $request->price ?? $request->max_price;
            $query->whereHas('flightClasses', function ($q) use ($price) {
                $q->where('price_per_seat', '<=', $price);
            });
        }

        if ($request->filled('min_price')) {
            $query->whereHas('flightClasses', function ($q) use ($request) {
                $q->where('price_per_seat', '>=', $request->min_price);
            });
        }


        if ($request->filled('aircraft')) {
            $searchTerm = strtolower($request->aircraft);
            $query->whereHas('aircraft', function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(model) LIKE ?', ["%$searchTerm%"]);
            });
        }


        if ($request->filled('q')) {
            $searchTerm = strtolower($request->q);
            $query->where(function ($q) use ($searchTerm) {

                $q->whereRaw('LOWER(flight_number) LIKE ?', ["%$searchTerm%"])

                    ->orWhereHas('carrier', function ($carrierQuery) use ($searchTerm) {
                        $carrierQuery->whereRaw('LOWER(carrier_name) LIKE ?', ["%$searchTerm%"])
                            ->orWhereRaw('LOWER(code) LIKE ?', ["%$searchTerm%"]);
                    })

                    ->orWhereHas('flightLegs.originAirport', function ($airportQuery) use ($searchTerm) {
                        $airportQuery->whereRaw('LOWER(city) LIKE ?', ["%$searchTerm%"])
                            ->orWhereRaw('LOWER(country) LIKE ?', ["%$searchTerm%"])
                            ->orWhereRaw('LOWER(airport_name) LIKE ?', ["%$searchTerm%"])
                            ->orWhereRaw('LOWER(airport_code) LIKE ?', ["%$searchTerm%"]);
                    })
                    ->orWhereHas('flightLegs.destinationAirport', function ($airportQuery) use ($searchTerm) {
                        $airportQuery->whereRaw('LOWER(city) LIKE ?', ["%$searchTerm%"])
                            ->orWhereRaw('LOWER(country) LIKE ?', ["%$searchTerm%"])
                            ->orWhereRaw('LOWER(airport_name) LIKE ?', ["%$searchTerm%"])
                            ->orWhereRaw('LOWER(airport_code) LIKE ?', ["%$searchTerm%"]);
                    })

                    ->orWhereHas('aircraft', function ($aircraftQuery) use ($searchTerm) {
                        $aircraftQuery->whereRaw('LOWER(model) LIKE ?', ["%$searchTerm%"]);
                    })

                    ->orWhereHas('flightClasses.class', function ($classQuery) use ($searchTerm) {
                        $classQuery->whereRaw('LOWER(class_name) LIKE ?', ["%$searchTerm%"]);
                    });
            });
        }
    }
    public function seatAvailability($flightId): JsonResponse
    {
        try {
            $flight = Flight::with([
                'aircraft',
                'flightSeats.class',
                'flightClasses.class'
            ])->findOrFail($flightId);


            $totalSeats = $flight->flightSeats->count();
            $availableSeats = $flight->flightSeats->where('status', 'available')->count();
            $reservedSeats = $flight->flightSeats->where('status', 'reserved')->count();
            $blockedSeats = $flight->flightSeats->where('status', 'blocked')->count();


            $seatsByClass = $flight->flightSeats->groupBy('class.class_name')->map(function ($seats, $className) {
                return [
                    'total_seats' => $seats->count(),
                    'available_seats' => $seats->where('status', 'available')->count(),
                    'reserved_seats' => $seats->where('status', 'reserved')->count(),
                    'blocked_seats' => $seats->where('status', 'blocked')->count(),
                    'availability_percentage' => round(($seats->where('status', 'available')->count() / $seats->count()) * 100, 2)
                ];
            });


            $seatsDetails = $flight->flightSeats->groupBy('class.class_name')->map(function ($seats, $className) {
                return [
                    'class_name' => $className,
                    'seats' => $seats->map(function ($seat) {
                        return [
                            'id' => $seat->id,
                            'seat_number' => $seat->seat_number,
                            'status' => $seat->status,
                            'price' => $seat->price,
                            'is_available' => $seat->status === 'available',
                        ];
                    })->sortBy('seat_number')->values()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'flight_info' => [
                        'id' => $flight->id,
                        'flight_number' => $flight->flight_number,
                        'aircraft_model' => $flight->aircraft->model,
                        'total_capacity' => $flight->aircraft->total_seats
                    ],
                    'seats_statistics' => [
                        'total_seats' => $totalSeats,
                        'available_seats' => $availableSeats,
                        'reserved_seats' => $reservedSeats,
                        'blocked_seats' => $blockedSeats,
                    ],
                    'seats_by_class' => $seatsByClass,
                    'seats_details' => $seatsDetails
                ],
                'message' => 'search for seats done successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'flight not found'], 404);
        }
    }
}
