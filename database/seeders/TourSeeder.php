<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourAvailability;
use App\Models\TourItinerary;
use App\Models\User;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'tours')->first();
        $admin = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->first() ?? User::first();

        if (! $category) {
            $this->command->warn('Tours category not found. Please run CategorySeeder first.');

            return;
        }

        $tours = [
            [
                'name_en' => 'Pyramids of Giza Tour',
                'name_ar' => 'جولة أهرامات الجيزة',
                'slug' => 'pyramids-of-giza-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 100.00,
                'child_price' => 50.00,
                'infant_price' => 0.00,
                'max_participants' => 30,
                'min_participants' => 2,
                'difficulty' => 'moderate',
                'languages' => ['en', 'ar'],
                'tags' => ['historical', 'cultural', 'ancient'],
                'included' => ['Transportation', 'Guide', 'Entrance fees'],
                'excluded' => ['Lunch', 'Tips'],
                'location_lat' => 29.9792,
                'location_lng' => 31.1342,
                'is_featured' => true,
            ],
            [
                'name_en' => 'Nile Cruise Experience',
                'name_ar' => 'تجربة رحلة نيلية',
                'slug' => 'nile-cruise-experience',
                'duration_days' => 3,
                'duration_nights' => 2,
                'adult_price' => 500.00,
                'child_price' => 250.00,
                'infant_price' => 0.00,
                'max_participants' => 50,
                'min_participants' => 4,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar'],
                'tags' => ['cruise', 'luxury', 'relaxation'],
                'included' => ['Accommodation', 'Meals', 'Entertainment'],
                'excluded' => ['Drinks', 'Tips'],
                'location_lat' => 30.0444,
                'location_lng' => 31.2357,
                'is_featured' => true,
            ],
        ];

        foreach ($tours as $tourData) {
            $tour = Tour::firstOrCreate(
                ['slug' => $tourData['slug']],
                [
                    'category_id' => $category->id,
                    'duration_days' => $tourData['duration_days'],
                    'duration_nights' => $tourData['duration_nights'],
                    'adult_price' => $tourData['adult_price'],
                    'child_price' => $tourData['child_price'],
                    'infant_price' => $tourData['infant_price'],
                    'max_participants' => $tourData['max_participants'],
                    'min_participants' => $tourData['min_participants'],
                    'difficulty' => $tourData['difficulty'],
                    'languages' => $tourData['languages'],
                    'tags' => $tourData['tags'],
                    'included' => $tourData['included'],
                    'excluded' => $tourData['excluded'],
                    'location_lat' => $tourData['location_lat'],
                    'location_lng' => $tourData['location_lng'],
                    'is_featured' => $tourData['is_featured'],
                    'status' => 'active',
                    'transport_included' => true,
                    'created_by' => $admin->id,
                ]
            );

            // Add translations
            $tour->translateOrNew('en')->name = $tourData['name_en'];
            $tour->translateOrNew('en')->short_description = "Experience {$tourData['name_en']}";
            $tour->translateOrNew('en')->description = "Full description for {$tourData['name_en']}";
            $tour->translateOrNew('en')->highlights = "Highlight 1\nHighlight 2\nHighlight 3";
            $tour->translateOrNew('en')->meeting_point = 'Hotel lobby';
            $tour->translateOrNew('en')->cancellation_policy = 'Free cancellation up to 24 hours before tour';
            $tour->translateOrNew('en')->terms_conditions = 'Terms and conditions apply';

            $tour->translateOrNew('ar')->name = $tourData['name_ar'];
            $tour->translateOrNew('ar')->short_description = "تجربة {$tourData['name_ar']}";
            $tour->translateOrNew('ar')->description = "وصف كامل لـ {$tourData['name_ar']}";
            $tour->translateOrNew('ar')->highlights = "ميزة 1\nميزة 2\nميزة 3";
            $tour->translateOrNew('ar')->meeting_point = 'لوبي الفندق';
            $tour->translateOrNew('ar')->cancellation_policy = 'إلغاء مجاني حتى 24 ساعة قبل الجولة';
            $tour->translateOrNew('ar')->terms_conditions = 'الشروط والأحكام تنطبق';

            $tour->save();

            // Create availability for next 30 days
            for ($i = 0; $i < 30; $i++) {
                TourAvailability::firstOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'date' => now()->addDays($i)->format('Y-m-d'),
                    ],
                    [
                        'available_slots' => $tourData['max_participants'],
                        'booked_slots' => 0,
                        'is_active' => true,
                    ]
                );
            }

            // Create itinerary
            for ($day = 1; $day <= $tourData['duration_days']; $day++) {
                $itinerary = TourItinerary::firstOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'day_number' => $day,
                    ],
                    [
                        'sort_order' => $day,
                    ]
                );

                $itinerary->translateOrNew('en')->title = "Day {$day}";
                $itinerary->translateOrNew('en')->description = "Day {$day} description";
                $itinerary->translateOrNew('en')->location = "Location for day {$day}";
                $itinerary->translateOrNew('en')->duration = '8 hours';

                $itinerary->translateOrNew('ar')->title = "اليوم {$day}";
                $itinerary->translateOrNew('ar')->description = "وصف اليوم {$day}";
                $itinerary->translateOrNew('ar')->location = "موقع اليوم {$day}";
                $itinerary->translateOrNew('ar')->duration = '8 ساعات';

                $itinerary->save();
            }

            // Create activities
            $activities = [
                ['name_en' => 'Sightseeing', 'name_ar' => 'مشاهدة المعالم', 'type' => 'sightseeing'],
                ['name_en' => 'Photography', 'name_ar' => 'التصوير', 'type' => 'photography'],
            ];

            foreach ($activities as $index => $activityData) {
                $activity = TourActivity::firstOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'activity_type' => $activityData['type'],
                    ],
                    [
                        'sort_order' => $index + 1,
                    ]
                );

                $activity->translateOrNew('en')->name = $activityData['name_en'];
                $activity->translateOrNew('en')->description = "Description for {$activityData['name_en']}";

                $activity->translateOrNew('ar')->name = $activityData['name_ar'];
                $activity->translateOrNew('ar')->description = "وصف لـ {$activityData['name_ar']}";

                $activity->save();
            }
        }

        $this->command->info('Tours seeded successfully!');
    }
}
