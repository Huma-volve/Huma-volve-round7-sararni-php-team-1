<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourAvailability;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tours = Tour::all();
        $customer = User::where('email', 'customer@test.com')->first();

        if ($tours->isEmpty() || ! $customer) {
            $this->command->warn('No tours found or customer user not found. Please run TourSeeder and TestUsersSeeder first.');

            return;
        }

        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'refunded'];
        $paymentMethods = ['cash', 'credit_card', 'bank_transfer', 'paypal'];

        // Create multiple bookings for customer user
        foreach ($tours as $tour) {
            // Create at least one completed booking per tour for reviews
            $bookingCount = 0;
            $hasCompleted = false;

            // Try to create 2-3 bookings per tour
            for ($i = 0; $i < rand(2, 3); $i++) {
                $availability = TourAvailability::where('tour_id', $tour->id)
                    ->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots')
                    ->first();

                if (! $availability) {
                    continue;
                }

                $adults = rand(1, 3);
                $children = rand(0, 2);
                $infants = rand(0, 1);

                $adultPrice = $tour->adult_price;
                $childPrice = $tour->child_price ?? 0;
                $infantPrice = $tour->infant_price ?? 0;

                $adultTotal = $adultPrice * $adults;
                $childTotal = $childPrice * $children;
                $infantTotal = $infantPrice * $infants;
                $subtotal = $adultTotal + $childTotal + $infantTotal;

                $discountAmount = 0;
                if ($tour->discount_percentage > 0) {
                    $discountAmount = ($subtotal * $tour->discount_percentage) / 100;
                }

                $totalAmount = $subtotal - $discountAmount;

                // Ensure at least one completed booking per tour
                if (! $hasCompleted && $i === 0) {
                    $status = 'completed';
                    $paymentStatus = 'paid';
                } else {
                    $status = $statuses[array_rand($statuses)];
                    $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                }

                if ($status === 'completed') {
                    $hasCompleted = true;
                }

                $paidAmount = $paymentStatus === 'paid' ? $totalAmount : ($paymentStatus === 'refunded' ? $totalAmount : 0);

                $booking = Booking::firstOrCreate(
                    [
                        'user_id' => $customer->id,
                        'tour_id' => $tour->id,
                        'tour_date' => $availability->date,
                    ],
                    [
                        'booking_number' => Booking::make()->generateBookingNumber(),
                        'tour_time' => '09:00:00',
                        'adults_count' => $adults,
                        'children_count' => $children,
                        'infants_count' => $infants,
                        'adult_price' => $adultPrice,
                        'child_price' => $childPrice,
                        'infant_price' => $infantPrice,
                        'discount_amount' => $discountAmount,
                        'total_amount' => $totalAmount,
                        'paid_amount' => $paidAmount,
                        'status' => $status,
                        'payment_status' => $paymentStatus,
                        'payment_method' => $paymentStatus === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null,
                        'payment_reference' => $paymentStatus === 'paid' ? 'REF-'.strtoupper(uniqid()) : null,
                        'special_requests' => rand(0, 1) ? 'Please provide vegetarian meals' : null,
                        'cancellation_reason' => $status === 'cancelled' ? 'Change of plans' : null,
                        'cancelled_at' => $status === 'cancelled' ? now()->subDays(rand(1, 10)) : null,
                    ]
                );

                // Update availability booked slots
                if ($status !== 'cancelled') {
                    $totalParticipants = $adults + $children + $infants;
                    $availability->increment('booked_slots', $totalParticipants);
                }

                $bookingCount++;
            }
        }

        $this->command->info('Bookings seeded successfully!');
    }
}
