<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::where('payment_status', 'paid')
            ->with(['user', 'tour'])
            ->get();

        if ($bookings->isEmpty()) {
            $this->command->warn('No paid bookings found. Creating payments from bookings...');

            $bookings = Booking::whereIn('payment_status', ['paid', 'pending'])
                ->with(['user', 'tour'])
                ->get();
        }

        $paymentMethods = ['card', 'cash', 'bank_transfer', 'paypal', 'stripe'];
        $statuses = ['pending', 'completed', 'failed'];

        $toursCategory = Category::where('slug', 'tours')->first();

        foreach ($bookings->take(15) as $booking) {
            $status = $booking->payment_status === 'paid' ? 'completed' : ($booking->payment_status === 'refunded' ? 'failed' : 'pending');

            Payment::firstOrCreate(
                [
                    'user_id' => $booking->user_id,
                    'category_id' => $toursCategory?->id,
                    'item_id' => $booking->id,
                ],
                [
                    'amount' => $booking->paid_amount > 0 ? $booking->paid_amount : $booking->total_amount,
                    'payment_method' => $booking->payment_method ?? $paymentMethods[array_rand($paymentMethods)],
                    'status' => $status,
                    'paid_at' => $status === 'completed' ? ($booking->created_at ?? now()->subDays(rand(1, 30))) : null,
                ]
            );
        }

        $this->command->info('Payments seeded successfully!');
    }
}
