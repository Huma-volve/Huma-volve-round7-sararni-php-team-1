<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $completedBookings = Booking::where('status', 'completed')
            ->with(['user', 'tour'])
            ->get();

        $customer = User::where('email', 'customer@test.com')->first();

        if ($completedBookings->isEmpty()) {
            $this->command->warn('No completed bookings found. Creating reviews from tours for customer user...');

            $tours = Tour::all();

            if (! $customer || $tours->isEmpty()) {
                $this->command->warn('Customer user or tours not found.');

                return;
            }

            foreach ($tours as $tour) {
                $rating = rand(3, 5);
                $status = ['approved', 'pending', 'approved'][array_rand(['approved', 'pending', 'approved'])];

                $review = Review::firstOrCreate(
                    [
                        'user_id' => $customer->id,
                        'tour_id' => $tour->id,
                    ],
                    [
                        'booking_id' => null,
                        'rating' => $rating,
                        'status' => $status,
                    ]
                );

                $titles = [
                    'en' => [
                        'Amazing experience!',
                        'Great tour, highly recommended',
                        'Wonderful trip',
                        'Excellent service',
                        'Memorable journey',
                    ],
                    'ar' => [
                        'تجربة رائعة!',
                        'جولة رائعة، أنصح بها بشدة',
                        'رحلة رائعة',
                        'خدمة ممتازة',
                        'رحلة لا تُنسى',
                    ],
                ];

                $comments = [
                    'en' => [
                        'This tour exceeded all my expectations. The guide was knowledgeable and friendly, and the itinerary was well-planned. I would definitely book again!',
                        'Amazing experience from start to finish. The locations were beautiful and the service was top-notch. Highly recommend!',
                        'Great value for money. The tour was well-organized and the staff were professional. Will come back for sure.',
                        'One of the best tours I\'ve ever been on. Everything was perfect from booking to completion.',
                        'Fantastic experience! The tour guide was excellent and made the whole trip enjoyable.',
                    ],
                    'ar' => [
                        'هذه الجولة تجاوزت جميع توقعاتي. المرشد كان على دراية وودود، والبرنامج كان مخططاً بشكل جيد. سأحجز مرة أخرى بالتأكيد!',
                        'تجربة رائعة من البداية إلى النهاية. المواقع كانت جميلة والخدمة كانت على أعلى مستوى. أنصح بشدة!',
                        'قيمة ممتازة مقابل المال. الجولة كانت منظمة بشكل جيد والموظفون كانوا محترفين. سأعود بالتأكيد.',
                        'واحدة من أفضل الجولات التي شاركت فيها على الإطلاق. كل شيء كان مثالياً من الحجز إلى الإتمام.',
                        'تجربة رائعة! المرشد السياحي كان ممتازاً وجعل الرحلة بأكملها ممتعة.',
                    ],
                ];

                $titleIndex = array_rand($titles['en']);
                $commentIndex = array_rand($comments['en']);

                $review->translateOrNew('en')->title = $titles['en'][$titleIndex];
                $review->translateOrNew('en')->comment = $comments['en'][$commentIndex];

                $review->translateOrNew('ar')->title = $titles['ar'][$titleIndex];
                $review->translateOrNew('ar')->comment = $comments['ar'][$commentIndex];

                $review->save();

                if ($status === 'approved') {
                    $tour->updateRating();
                }
            }
        } else {
            // Only create reviews for customer user's completed bookings
            $customerCompletedBookings = $completedBookings->where('user_id', $customer?->id ?? 0);

            if ($customerCompletedBookings->isEmpty()) {
                $this->command->warn('No completed bookings found for customer user. Creating reviews from tours...');

                $tours = Tour::all();
                if (! $customer || $tours->isEmpty()) {
                    return;
                }

                foreach ($tours as $tour) {
                    $rating = rand(4, 5);
                    $status = 'approved';

                    $review = Review::firstOrCreate(
                        [
                            'user_id' => $customer->id,
                            'tour_id' => $tour->id,
                        ],
                        [
                            'booking_id' => null,
                            'rating' => $rating,
                            'status' => $status,
                        ]
                    );

                    $titles = [
                        'en' => ['Amazing experience!', 'Great tour, highly recommended', 'Wonderful trip'],
                        'ar' => ['تجربة رائعة!', 'جولة رائعة، أنصح بها بشدة', 'رحلة رائعة'],
                    ];

                    $comments = [
                        'en' => [
                            'This tour exceeded all my expectations. The guide was knowledgeable and friendly.',
                            'Amazing experience from start to finish. Highly recommend!',
                            'Great value for money. Will come back for sure.',
                        ],
                        'ar' => [
                            'هذه الجولة تجاوزت جميع توقعاتي. المرشد كان على دراية وودود.',
                            'تجربة رائعة من البداية إلى النهاية. أنصح بشدة!',
                            'قيمة ممتازة مقابل المال. سأعود بالتأكيد.',
                        ],
                    ];

                    $titleIndex = array_rand($titles['en']);
                    $commentIndex = array_rand($comments['en']);

                    $review->translateOrNew('en')->title = $titles['en'][$titleIndex];
                    $review->translateOrNew('en')->comment = $comments['en'][$commentIndex];

                    $review->translateOrNew('ar')->title = $titles['ar'][$titleIndex];
                    $review->translateOrNew('ar')->comment = $comments['ar'][$commentIndex];

                    $review->save();
                    $tour->updateRating();
                }

                return;
            }

            foreach ($customerCompletedBookings as $booking) {
                // Check if review already exists
                $existingReview = Review::where('booking_id', $booking->id)->first();
                if ($existingReview) {
                    continue;
                }

                $rating = rand(3, 5);
                $status = ['approved', 'pending', 'approved'][array_rand(['approved', 'pending', 'approved'])];

                $review = Review::create([
                    'user_id' => $booking->user_id,
                    'tour_id' => $booking->tour_id,
                    'booking_id' => $booking->id,
                    'rating' => $rating,
                    'status' => $status,
                ]);

                $titles = [
                    'en' => [
                        'Amazing experience!',
                        'Great tour, highly recommended',
                        'Wonderful trip',
                        'Excellent service',
                        'Memorable journey',
                    ],
                    'ar' => [
                        'تجربة رائعة!',
                        'جولة رائعة، أنصح بها بشدة',
                        'رحلة رائعة',
                        'خدمة ممتازة',
                        'رحلة لا تُنسى',
                    ],
                ];

                $comments = [
                    'en' => [
                        'This tour exceeded all my expectations. The guide was knowledgeable and friendly.',
                        'Amazing experience from start to finish. Highly recommend!',
                        'Great value for money. Will come back for sure.',
                        'One of the best tours I\'ve ever been on.',
                        'Fantastic experience! The tour guide was excellent.',
                    ],
                    'ar' => [
                        'هذه الجولة تجاوزت جميع توقعاتي. المرشد كان على دراية وودود.',
                        'تجربة رائعة من البداية إلى النهاية. أنصح بشدة!',
                        'قيمة ممتازة مقابل المال. سأعود بالتأكيد.',
                        'واحدة من أفضل الجولات التي شاركت فيها على الإطلاق.',
                        'تجربة رائعة! المرشد السياحي كان ممتازاً.',
                    ],
                ];

                $titleIndex = array_rand($titles['en']);
                $commentIndex = array_rand($comments['en']);

                $review->translateOrNew('en')->title = $titles['en'][$titleIndex];
                $review->translateOrNew('en')->comment = $comments['en'][$commentIndex];

                $review->translateOrNew('ar')->title = $titles['ar'][$titleIndex];
                $review->translateOrNew('ar')->comment = $comments['ar'][$commentIndex];

                $review->save();

                if ($status === 'approved') {
                    $booking->tour->updateRating();
                }
            }
        }

        $this->command->info('Reviews seeded successfully!');
    }
}
