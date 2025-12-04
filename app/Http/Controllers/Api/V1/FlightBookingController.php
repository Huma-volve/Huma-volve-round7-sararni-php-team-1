<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\FlightBookingService;
use App\Http\Resources\Api\V1\FlightResource;
use App\Http\Resources\Api\V1\BookingResource;
use App\Http\Resources\Api\V1\FlightDetailResource;

class FlightBookingController extends Controller
{
    protected $bookingService;

    public function __construct(FlightBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function bookFlight(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'flight_id' => 'required|exists:flights,id',
            'trip_type' => 'required|in:one_way,round_trip,multi_city',

            // Participants
            'participants' => 'required|array|min:1',
            'participants.*.title' => 'required|in:Mr,Mrs,Ms',
            'participants.*.first_name' => 'required|string|max:100',
            'participants.*.last_name' => 'required|string|max:100',
            'participants.*.date_of_birth' => 'required|date',
            'participants.*.passport_number' => 'required|string|max:50',
            'participants.*.passport_expiry' => 'required|date',
            'participants.*.nationality' => 'required|string|max:100',
            'participants.*.email' => 'required|email',
            'participants.*.phone' => 'required|string|max:20',
            'participants.*.type' => 'required|in:adult,child,infant',
            'participants.*.special_requests' => 'sometimes|string|nullable',

          
            'segments' => 'required|array|min:1',
            'segments.*.seat_id' => 'required|exists:flight_seats,id',
            'segments.*.flight_id' => 'required|exists:flights,id',
            'segments.*.direction' => 'required|in:outbound,return,segment',
            'segments.*.participant_index' => 'required|integer|min:0',

        ]);




       
        $seatIds = array_column($data['segments'], 'seat_id');
        if (count($seatIds) !== count(array_unique($seatIds))) {
            return response()->json([
                'status' => false,
                'message' => 'can not reserve the same seat many time'
            ], 400);
        }

        try {
            $booking = $this->bookingService->create($data);

            return response()->json([
                'status' => true,
                'message' => 'booking done successfully',
                'data' => [
                    'booking' => new BookingResource($booking['booking']),
                    'flight' => new FlightResource($booking['flight']),
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'error with flight booking' . $e->getMessage()
            ], 500);
        }
    }

    
    public function confirmBooking($id)
    {
        try {
            $booking = $this->bookingService->confirmBooking($id);

            return response()->json([
                'status' => true,
                'message' => 'booking confirmed successfully',
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'error with confirming booking ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function cancelBooking($id)
    {
        try {
            $booking = $this->bookingService->cancelBooking($id);

            return response()->json([
                'status' => true,
                'message' => 'booking canceled successfully',
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'error with canceling this booking ' . $e->getMessage()
            ], 500);
        }
    }

   
    public function show($id)
    {
        try {
            $booking = $this->bookingService->findBooking($id);

            return response()->json([
                'status' => true,
                'message' => 'Booking data was successfully retrieved',
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'The reservation is not available'
            ], 404);
        }
    }

   
    public function updatePaymentStatus(Request $request, $id)
    {
        $data = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded,partial',
            'payment_method' => 'sometimes|string|nullable',
            'payment_reference' => 'sometimes|string|nullable'
        ]);

        try {
            $booking = $this->bookingService->updatePaymentStatus($id, $data);

            return response()->json([
                'status' => true,
                'message' => 'Payment status updated successfully',
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment status failed:' . $e->getMessage()
            ], 500);
        }
    }

    
    public function userBookings($userId)
    {
        try {
            $bookings = $this->bookingService->getUserBookings($userId);

            return response()->json([
                'status' => true,
                'message' => 'User bookings successfully retrieved',
                'data' => BookingResource::collection($bookings)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to bring in bookings:' . $e->getMessage()
            ], 500);
        }
    }

    
    public function search(Request $request)
    {
        try {
            $bookings = $this->bookingService->searchBookings($request->all());

            return response()->json([
                'status' => true,
                'message' =>'Search successful',
                'data' => [
                    'results' => BookingResource::collection($bookings),
                    'count' => $bookings->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' =>'Search failed:' . $e->getMessage()
            ], 500);
        }
    }
}
