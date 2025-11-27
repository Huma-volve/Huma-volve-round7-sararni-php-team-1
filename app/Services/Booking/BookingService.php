<?php

namespace App\Services\Booking;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;


class BookingService  {

    use  ApiResponseTrait;
     public function ensureRoomAvailable($roomId, $start, $end)
    {
        $room = Room::findOrFail($roomId);

        $bookings = $room->bookings()->where('status', '!=', 'cancelled')
           ->where(function ($query) use ($start, $end) {
            $query->whereBetween('check_in_date', [$start, $end])
                ->orWhereBetween('check_out_date', [$start, $end])
                ;
        })->get();

        $bookings->each(function ($booking)  use ($start, $end) {
            if ($booking->check_in_date < $end && $booking->check_out_date > $start) {
                throw new \Exception('Room is not available for the specified dates.');
            }
        });
        return true;
    }

    public function validateOccupancy($roomId, $adults, $children, $infants)
    {
        $room = Room::findOrFail($roomId);
        $max = json_decode($room->occupancy, true);

        if ($adults > $max['adults'] || $children > $max['children'] || $infants > $max['infants']) {
            throw new \Exception('Room is not available for the specified occupancy.');
        }

        return true;
    }

        public function calculateTotal($roomId, $ratePlan)
    {
        $room = Room::find($roomId);

        $days = Carbon::parse(request('check_in_date'))
            ->diffInDays(Carbon::parse(request('check_out_date'))) ? : 1;

        return $room->price_per_night * $days + $ratePlan->base_price;
    }

        public function createBooking($data)
    {
        // dd($data);
             return Booking::create($data);
    }



}
