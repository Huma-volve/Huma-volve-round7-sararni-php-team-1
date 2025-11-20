<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Booking\CancelBookingRequest;
use App\Http\Requests\Api\V1\Booking\CheckAvailabilityRequest;
use App\Http\Requests\Api\V1\Booking\ConfirmBookingRequest;
use App\Http\Requests\Api\V1\Booking\CreateBookingRequest;
use App\Http\Resources\Api\V1\BookingResource;
use App\Models\Booking;
use App\Models\Tour;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function checkAvailability(CheckAvailabilityRequest $request): JsonResponse
    {
        try {
            $result = $this->bookingService->checkAvailability(
                $request->tour_id,
                $request->date,
                $request->time,
                $request->adults,
                $request->children ?? 0,
                $request->infants ?? 0
            );

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'AVAILABILITY_CHECK_FAILED',
                $e->getMessage(),
                null,
                400
            );
        }
    }

    public function calculatePrice(CheckAvailabilityRequest $request): JsonResponse
    {
        try {
            $tour = Tour::findOrFail($request->tour_id);
            $priceData = $this->bookingService->calculatePrice(
                $tour,
                $request->adults,
                $request->children ?? 0,
                $request->infants ?? 0,
                $request->date
            );

            return response()->json([
                'success' => true,
                'data' => $priceData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PRICE_CALCULATION_FAILED',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    public function store(CreateBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking([
                'user_id' => $request->user()->id,
                'tour_id' => $request->tour_id,
                'date' => $request->date,
                'time' => $request->time,
                'adults' => $request->adults,
                'children' => $request->children ?? 0,
                'infants' => $request->infants ?? 0,
                'special_requests' => $request->special_requests,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('messages.booking.created'),
                'data' => new BookingResource($booking->load(['details', 'participants'])),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'BOOKING_CREATION_FAILED',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    public function confirm(ConfirmBookingRequest $request, int $id): JsonResponse
    {
        try {
            $booking = Booking::where('user_id', $request->user()->id)->findOrFail($id);
            $booking = $this->bookingService->confirmBooking($booking->id, $request->validated());

            return $this->successResponse(
                new BookingResource($booking->load(['details', 'participants', 'item'])),
                __('messages.booking.confirmed')
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'BOOKING_CONFIRMATION_FAILED',
                $e->getMessage(),
                null,
                400
            );
        }
    }

    public function index(Request $request): JsonResponse
    {
        $query = Booking::query()
            ->with(['details', 'participants'])
            ->where('user_id', $request->user()->id);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }

        $pageSize = $request->input('page_size', 20);
        $bookings = $query->orderBy('created_at', 'desc')
            ->paginate($pageSize);

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
                'last_page' => $bookings->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $booking = Booking::with(['details', 'participants', 'item'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (! $booking) {
            return $this->errorResponse(
                'BOOKING_NOT_FOUND',
                __('messages.booking.not_found'),
                null,
                404
            );
        }

        return $this->successResponse(new BookingResource($booking));
    }

    public function cancel(CancelBookingRequest $request, int $id): JsonResponse
    {
        try {
            $booking = Booking::where('user_id', $request->user()->id)->findOrFail($id);
            $booking = $this->bookingService->cancelBooking($booking->id, $request->reason);

            return response()->json([
                'success' => true,
                'message' => __('messages.booking.cancelled'),
                'data' => new BookingResource($booking->load(['details', 'participants'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'BOOKING_CANCELLATION_FAILED',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }
}
