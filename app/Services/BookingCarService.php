<?php 

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;

class BookingCarService{

    public function checkCarAvailability(array $data){
        $car =Car::findOrFail($data['item_id']);

        $checkAvailability = Booking::where('item_id',$data['item_id'])
        ->where('category', 'car')
         ->whereIn('status', ['confirmed'])
        ->where(function ($query) use ($data) {
            $query->where('pickup_date', '<=', $data['dropoff_date'])
                ->where('dropoff_date', '>=', $data['pickup_date']);
        })
        ->exists();

        return !$checkAvailability;    
    }

    public function calculatePriceByHours($car, $pickupDate, $dropoffDate) {
        $pickup = Carbon::parse($pickupDate);
        $dropoff = Carbon::parse($dropoffDate);

        $hours = $pickup->diffInHours($dropoff);
        $totalPrice = 0;

        if ($hours > 0) {
            $hourTier = $car->priceTiers()
                ->where('from_hours', '<=', $hours)
                ->where('to_hours', '>=', $hours)
                ->first();

            if (!$hourTier) {
                $hourTier = $car->priceTiers()->orderBy('to_hours', 'desc')->first();
            }

            if ($hourTier) {
                $totalPrice = $hourTier->price_per_hour * $hours;
            } else {
                throw new \Exception("No price tier found for hours");
            }
        }

        return $totalPrice;
    }

    public function calculatePriceByDays($car, $pickupDate, $dropoffDate) {
        $pickup = Carbon::parse($pickupDate);
        $dropoff = Carbon::parse($dropoffDate);

        $hours = $pickup->diffInHours($dropoff);
        $fullDays = ceil($hours / 24); 

        if ($fullDays > 0) {
            $dayTier = $car->priceTiers()
                ->where('from_hours', '<=', 24)
                ->where('to_hours', '>=', 24)
                ->first();

            if ($dayTier) {
                return $dayTier->price_per_day * $fullDays;
            } else {
                throw new \Exception("No price tier found for daily rate");
            }
        }

        return 0;
    }


     public function createCarBooking(array $data, $pricingMethod = 'hours') {
        $car = Car::findOrFail($data['item_id']);

        $totalPrice = 0;
        if ($pricingMethod === 'days') {
            $totalPrice = $this->calculatePriceByDays($car, $data['pickup_date'], $data['dropoff_date']);
        } else {
            $totalPrice = $this->calculatePriceByHours($car, $data['pickup_date'], $data['dropoff_date']);
        }

        return Booking::create([
            'user_id'      => $data['user_id'],
            'booking_reference' => uniqid('BOOK_'),
            'category'     => 'car',
            'item_id'      => $data['item_id'],
            'pickup_date'  => $data['pickup_date'],
            'dropoff_date' => $data['dropoff_date'],
            'total_price'  => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending',
            'booking_date' => Carbon::now(),
        ]);
    }

    public function confirmBooking(int $bookingId , array $paymentData){
        $booking = Booking::findOrFail($bookingId);

            if ($booking->status !== 'pending') {
                return ApiResponse::errorResponse("Invalid booking status.", 500);
            }

    }


}
