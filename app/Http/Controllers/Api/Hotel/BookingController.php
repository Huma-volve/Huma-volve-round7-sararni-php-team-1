<?php

namespace App\Http\Controllers\Api\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\RatePlan;
use App\Models\Room;
use App\Models\User;
use App\Services\Booking\BookingService;
use App\Traits\Http\ApiResponseTrait  as TraitsApiResponseTrait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

    class BookingController extends Controller
{
    use ApiResponseTrait;

        protected $serviceBooking;

    public function __construct(BookingService $serviceBooking)
    {
        $this->serviceBooking = $serviceBooking;
    }


       public function store(StoreBookingRequest $request)
    {

       $validated = $request->validated();
    //    dd(  $validated);

     $hotel = Hotel::findOrFail($validated['hotel_id']);

        // 1) check occupancy
     $roomOccupancy = $this->serviceBooking->validateOccupancy($validated['room_id'], $validated['adults'], $validated['children'], $validated['infants']);

        // 2) check if room is free
     $rommAvailable = $this->serviceBooking->ensureRoomAvailable($validated['room_id'], $validated['check_in_date'], $validated['check_out_date']);
         // 3) calculate price
        $rate = RatePlan::find($validated['rate_plan_id']);

        $total = $this->serviceBooking->calculateTotal($validated['room_id'], $rate);

        // 4) create booking
        $booking = $this->serviceBooking->createBooking([
            'user_id'       =>2,
            'category_id'   => 'hotel',
            'item_id'        => $hotel->id,
            'currency' => config('app.currency', 'USD'),
            'room_id'       => (int)  $validated['room_id'],
            'rate_plan_id'  => $validated['rate_plan_id'],
            'check_in_date'    => $validated['check_in_date'],
            'check_out_date'      => $validated['check_out_date'],
            'booking_date'     => now(),
            'total_price'   => $total,
            'status'        => 'pending',
            'payment_status' => 'pending',
        ]);
            // dd($booking);

        $booking->details()->create([
             'meta' => [
                 'adults_count' => $validated['adults'],
                 'children_count' => $validated['children'] ?? 0,
                 'infants_count' => $validated['infants'] ?? 0,
             ]
        ]);

        return $this->successResponse(new BookingResource($booking), 'Booking created successfully');
    }

    public function confirm($id)
    {
       try{
                $booking = $this->serviceBooking->confirmBooking($id);

                if(!$booking){
                    return $this->errorResponse('Booking Not Found' , 404);
                }

        return  $this->successResponse(new BookingResource($booking ));

       }catch(\Exception $e){
           return $this->errorResponse($e->getMessage(), 500);
       }
    }

    public function cancel($id)
    {
        try{

            $booking = $this->serviceBooking->cancelBooking($id);

            if(!$booking){
                return $this->errorResponse('Booking Not Found' , 404);
            }

        return  $this->successResponse(new BookingResource($booking ));

        }catch(\Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


























    //  public function store(Request $request){

    //    $validated = $request->validate([
    //             'hotel_id'      => 'required|exists:hotels,id',
    //             'room_id'       => 'required|exists:rooms,id',
    //             'rate_plan_id'  => 'required|exists:rate_plans,id',
    //             'start_date'    => 'required|date',
    //             'end_date'      => 'required|date|after_or_equal:start_date',
    //             'guest_details' =>   'required|array'
    //         ]);


    //         $room = Room::findOrFail($validated['room_id']);

    //         $max = json_decode($room->occupancy, true);
    //         // dd($max);
    //     $requested = $validated['guest_details'];

    //         if($requested['adults']  >  $max['adults']){
    //         return response()->json(['message' => 'عدد البالغين أكبر من سعة الغرفة'], 422);
    //         }

    //         if($requested['children']  >  $max['children']){
    //         return response()->json(['message' => 'عدد الاطفال أكبر من سعة الغرفة'], 422);
    //         }

    //         if($requested['infants']  >  $max['infants']){
    //         return response()->json(['message' => 'عدد الرضع أكبر من سعة الغرفة'], 422);
    //         }


    // $totalPrice =  $this->calculateTotalPrice(
    //                         $validated['room_id'],
    //                         $validated['rate_plan_id'],
    //                         $validated['start_date'],
    //                         $validated['end_date']
    //             );


    //         $isBooked = Booking::where('item_id', $validated['room_id'])
    //                 ->whereIn('status', ['pending', 'confirmed']) // لا نسخة محجوزة ولا pending
    //                 ->where(function ($query) use ($validated) {
    //                     $query->where('start_date', '<=', $validated['end_date'])
    //                         ->where('end_date', '>=', $validated['start_date']);
    //                 })
    //                 ->exists();

    //             if ($isBooked) {
    //                 return response()->json([
    //                     'message' => 'Room is not available for the selected dates'
    //                 ], 422);
    //             }


    //         $booking =  Booking::create([
    //             'user_id'       =>  User::find(1)->id,
    //             'category_id'   => $validated['hotel_id'], // الفندق
    //             'item_id'       => $validated['room_id'],  // الغرفة
    //             'rate_plan_id'  => $validated['rate_plan_id'],
    //             'start_date'    => $validated['start_date'],
    //             'end_date'      => $validated['end_date'],
    //             'total_price'   => $totalPrice,
    //             'guest_details' => $validated['guest_details'] ,
    //             'status'        => 'pending',
    //            'payment_status' => 'pending',
    //         ]);


    //         return response()->json([
    //             'message' => 'Booking created successfully',
    //             'data' => $booking
    //         ], 201);


    //     }


//         private function calculateTotalPrice($roomId, $ratePlanId, $start, $end)
//         {
//             $room = Room::findOrFail($roomId);
//             $ratePlan = RatePlan::findOrFail($ratePlanId);

//             $days = (new \Carbon\Carbon($start))->diffInDays(new \Carbon\Carbon($end)) ?: 1;

//             $basePrice = $room->price_per_night * $days;

//             $planExtra = $ratePlan->base_price ?? 0;


//             return $basePrice  + $planExtra;
//         }






//       public function confirm($id)
// {
//     $booking = Booking::findOrFail($id);

//     if ($booking->status !== 'pending') {
//         return response()->json(['message' => 'Booking already confirmed or cancelled'], 400);
//     }

//     $booking->update([
//         'status' => 'confirmed',
//         'payment_status' => 'paid'
//     ]);

//     return response()->json([
//         'message' => 'Booking confirmed successfully',
//         'booking' => $booking
//     ]);
// }



// public function cancel($id)
// {
//     $booking = Booking::findOrFail($id);

//     if ($booking->status === 'cancelled') {
//         return response()->json(['message' => 'Booking already cancelled'], 400);
//     }

//     $booking->update([
//         'status' => 'canceled'
//     ]);

//     return response()->json([
//         'message' => 'Booking cancelled successfully',
//         'booking' => $booking
//     ]);
// }



    }
