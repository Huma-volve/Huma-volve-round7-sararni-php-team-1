<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;

use App\Http\Requests\StoreBookingCarRequest;
use App\Http\Requests\StoreCarRequest;
use App\Models\Car;
use App\Models\CarPriceTier;
use App\Services\BookingCarService;

use App\Services\CarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{

    // Show All Cars
    public function index(Request $request)
    {
        try{

            $query = Car::with(['brand','dropoffLocation','pickupLocation','priceTiers'])

            ->latest();

            // filter by brand name
            if($request->filled('brand_name')){
                $query->whereHas('brand', function($q) use ($request){
                    $q->where('name', 'LIKE', '%'. $request->brand_name .'%');
                });
            }

            // filter by model
            if($request->filled('car_model')){
                $query->where('model', 'LIKE',  '%'. $request->car_model .'%' );
            }

            $cars = $query->get();
            return ApiResponse::successResponse($cars, 'All Cars retrieved successfully');

        }catch(\Throwable $th){
            return ApiResponse::errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarRequest $request, CarService $carService)
    {
        try{

            $data = $request->validated();

            $car = $carService->createCar($data);

            return ApiResponse::successResponse($car, ' Car created successfully');

        }catch(\Throwable $th){

            return ApiResponse::errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        try{

            $car = Car::with(['brand','dropoffLocation','pickupLocation','priceTiers'])->find($id);

            if (!$car) {
                return ApiResponse::errorResponse('Car not found', 404);
            }

            return ApiResponse::successResponse($car, 'Car retrieved successfully');


        }catch(\Throwable $th){
            return ApiResponse::errorResponse($th->getMessage(), 500);
        }
    }


    public function availability(Request $request, $car_id, BookingCarService $bookingCarService)
    {

        try{
            $request->validate([
                'pickup_date' => 'required|date',
                'dropoff_date' => 'required|date|after:pickup_date',
                'pricing_method' => 'nullable|in:hours,days'
            ]);
            $car = Car::with(['brand','dropoffLocation','pickupLocation','priceTiers'])->find($car_id);

            if (!$car) {
                return ApiResponse::errorResponse( "Car not found", 404);
            }
             $data = [
                'item_id' => $car_id,
                'pickup_date' => $request->pickup_date,
                'dropoff_date' => $request->dropoff_date
            ];

            $isAvailable = $bookingCarService->checkCarAvailability($data);

            $pricingMethod = $request->pricing_method ?? 'hours';
        $totalPrice = ($pricingMethod === 'days')
            ? $bookingCarService->calculatePriceByDays($car, $request->pickup_date, $request->dropoff_date)
            : $bookingCarService->calculatePriceByHours($car, $request->pickup_date, $request->dropoff_date);

            return ApiResponse::successResponse([
                'is_available' => $isAvailable,
                'total_price' => $totalPrice,
                'pricing_method' => $pricingMethod,
                'car' => $car
            ], 'Availability checked successfully');

        }catch(\Throwable $th){
            return ApiResponse::errorResponse($th->getMessage(), 500);
        }
    }


    // Get Cars By Brand
    public function showByBrandID($brand_id)
    {

        try{
            $cars = Car::where('brand_id',$brand_id)->get();

           if ($cars->isEmpty()) {
                return ApiResponse::errorResponse('Cars not found for this brand', 404);
            }

            return ApiResponse::successResponse($cars, 'Car retrieved successfully');


        }catch(\Throwable $th){
            return ApiResponse::errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function booking_car(StoreBookingCarRequest $request, BookingCarService $bookingCarService)
    {

        $data = $request->validated();
        if (!$bookingCarService->checkCarAvailability($data)) {
            return ApiResponse::errorResponse('The car is not available for the selected dates.', 409);
        }
        $booking = $bookingCarService->createCarBooking($data);

         return ApiResponse::successResponse($booking, 'Car booked successfully.');



    }
}
