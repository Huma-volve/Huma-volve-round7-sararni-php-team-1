<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourAvailability;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function checkAvailability(int $tourId, string $date, ?string $time, int $adults, int $children = 0, int $infants = 0): array
    {
        $tour = Tour::findOrFail($tourId);

        if ($tour->status !== 'active') {
            return [
                'available' => false,
                'available_slots' => 0,
                'message' => __('messages.booking.tour_not_active'),
            ];
        }

        $totalParticipants = $adults + $children + $infants;

        if ($totalParticipants < $tour->min_participants) {
            return [
                'available' => false,
                'available_slots' => 0,
                'message' => __('messages.booking.min_participants', ['min' => $tour->min_participants]),
            ];
        }

        if ($totalParticipants > $tour->max_participants) {
            return [
                'available' => false,
                'available_slots' => 0,
                'message' => __('messages.booking.max_participants', ['max' => $tour->max_participants]),
            ];
        }

        $availability = TourAvailability::where('tour_id', $tourId)
            ->where('date', $date)
            ->where('is_active', true)
            ->first();

        if (! $availability) {
            return [
                'available' => false,
                'available_slots' => 0,
                'message' => __('messages.booking.date_not_available'),
            ];
        }

        $availableSlots = $availability->getAvailableSlots();

        if ($availableSlots < $totalParticipants) {
            return [
                'available' => false,
                'available_slots' => $availableSlots,
                'message' => __('messages.booking.insufficient_slots', ['available' => $availableSlots, 'required' => $totalParticipants]),
            ];
        }

        return [
            'available' => true,
            'available_slots' => $availableSlots,
            'message' => __('messages.booking.available'),
        ];
    }

    public function calculatePrice(Tour $tour, int $adults, int $children = 0, int $infants = 0, ?string $date = null): array
    {
        return $tour->calculatePrice($adults, $children, $infants, $date);
    }

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $tour = Tour::findOrFail($data['tour_id']);

            // Check availability with lock
            $availability = TourAvailability::where('tour_id', $tour->id)
                ->where('date', $data['date'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (! $availability) {
                throw new \Exception(__('messages.booking.date_not_available'));
            }

            $totalParticipants = $data['adults'] + ($data['children'] ?? 0) + ($data['infants'] ?? 0);

            if (! $availability->isAvailable($totalParticipants)) {
                throw new \Exception(__('messages.booking.insufficient_slots'));
            }

            // Calculate price
            $priceData = $this->calculatePrice($tour, $data['adults'], $data['children'] ?? 0, $data['infants'] ?? 0, $data['date']);

            // Book slots atomically
            if (! $availability->bookSlots($totalParticipants)) {
                throw new \Exception(__('messages.booking.booking_failed'));
            }

            // Create booking
            $booking = Booking::create([
                'user_id' => $data['user_id'],
                'tour_id' => $tour->id,
                'tour_date' => $data['date'],
                'tour_time' => $data['time'] ?? null,
                'adults_count' => $data['adults'],
                'children_count' => $data['children'] ?? 0,
                'infants_count' => $data['infants'] ?? 0,
                'adult_price' => $priceData['adult_price'],
                'child_price' => $priceData['child_price'],
                'infant_price' => $priceData['infant_price'],
                'discount_amount' => $priceData['discount_amount'],
                'total_amount' => $priceData['total'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'special_requests' => $data['special_requests'] ?? null,
            ]);

            // Update tour booking count
            $tour->increment('total_bookings');

            return $booking;
        });
    }

    public function confirmBooking(int $bookingId, array $paymentData): Booking
    {
        return DB::transaction(function () use ($bookingId, $paymentData) {
            $booking = Booking::findOrFail($bookingId);

            if ($booking->status !== 'pending') {
                throw new \Exception(__('messages.booking.invalid_status'));
            }

            // Process payment (placeholder - integrate with payment gateway)
            // TODO: Integrate with payment gateway

            // Update booking
            $booking->markAsPaid($paymentData['payment_method'], $paymentData['payment_reference'] ?? null);
            $booking->confirm();

            // TODO: Send confirmation email

            return $booking->fresh();
        });
    }

    public function cancelBooking(int $bookingId, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($bookingId, $reason) {
            $booking = Booking::with('tour')->findOrFail($bookingId);

            if (! $booking->canBeCancelled()) {
                throw new \Exception(__('messages.booking.cannot_cancel'));
            }

            $booking->cancel($reason);

            // Process refund if payment was made (placeholder)
            if ($booking->payment_status === 'paid') {
                // TODO: Integrate with payment gateway for refund
                $booking->update(['payment_status' => 'refunded']);
            }

            // TODO: Send cancellation email

            return $booking->fresh();
        });
    }
}
