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

            // Paris Tours (as shown in the design)
            [
                'name_en' => 'Paris Evening Cruise',
                'name_ar' => 'رحلة مسائية في باريس',
                'slug' => 'paris-evening-cruise',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 75.00,
                'child_price' => 50.00,
                'infant_price' => 0.00,
                'max_participants' => 50,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar', 'fr'],
                'tags' => ['romantic', 'evening', 'cruise', 'river'],
                'included' => ['Boat ride', 'Guide', 'Audio commentary'],
                'excluded' => ['Dinner', 'Drinks'],
                'location_lat' => 48.8566,
                'location_lng' => 2.3522,
                'is_featured' => true,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Boat'],
            ],
            [
                'name_en' => 'Iconic Paris Tour',
                'name_ar' => 'جولة باريس الأيقونية',
                'slug' => 'iconic-paris-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 50.00,
                'child_price' => 30.00,
                'infant_price' => 0.00,
                'max_participants' => 30,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar', 'fr'],
                'tags' => ['sightseeing', 'landmarks', 'iconic'],
                'included' => ['Guide', 'Map'],
                'excluded' => ['Transportation', 'Entrance fees'],
                'location_lat' => 48.8566,
                'location_lng' => 2.3522,
                'is_featured' => true,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Walking'],
            ],
            [
                'name_en' => 'Paris Art & Culture Tour',
                'name_ar' => 'جولة الفن والثقافة في باريس',
                'slug' => 'paris-art-culture-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 60.00,
                'child_price' => 35.00,
                'infant_price' => 0.00,
                'max_participants' => 25,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar', 'fr'],
                'tags' => ['art', 'culture', 'museums', 'galleries'],
                'included' => ['Museum tickets', 'Guide'],
                'excluded' => ['Transportation', 'Lunch'],
                'location_lat' => 48.8606,
                'location_lng' => 2.3376,
                'is_featured' => true,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Art historian', 'transportation' => 'Walking'],
            ],
            [
                'name_en' => 'Paris Historical Sites',
                'name_ar' => 'المواقع التاريخية في باريس',
                'slug' => 'paris-historical-sites',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 45.00,
                'child_price' => 25.00,
                'infant_price' => 0.00,
                'max_participants' => 20,
                'min_participants' => 1,
                'difficulty' => 'moderate',
                'languages' => ['en', 'ar', 'fr'],
                'tags' => ['historical', 'notre dame', 'latin quarter'],
                'included' => ['Guide', 'Entrance fees'],
                'excluded' => ['Transportation', 'Lunch'],
                'location_lat' => 48.8530,
                'location_lng' => 2.3499,
                'is_featured' => false,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Historian', 'transportation' => 'Walking'],
            ],
            [
                'name_en' => 'Paris Louvre Museum Tour',
                'name_ar' => 'جولة متحف اللوفر في باريس',
                'slug' => 'paris-louvre-museum-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 55.00,
                'child_price' => 30.00,
                'infant_price' => 0.00,
                'max_participants' => 15,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar', 'fr'],
                'tags' => ['louvre', 'art', 'museum', 'mona lisa'],
                'included' => ['Museum tickets', 'Guide'],
                'excluded' => ['Transportation', 'Audio guide'],
                'location_lat' => 48.8606,
                'location_lng' => 2.3376,
                'is_featured' => true,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Art expert', 'transportation' => 'Walking'],
            ],
            // Additional Paris Tours
            [
                'name_en' => 'Paris Food & Wine Tour',
                'name_ar' => 'جولة الطعام والنبيذ في باريس',
                'slug' => 'paris-food-wine-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 85.00,
                'child_price' => 45.00,
                'infant_price' => 0.00,
                'max_participants' => 12,
                'min_participants' => 2,
                'difficulty' => 'easy',
                'languages' => ['en', 'fr'],
                'tags' => ['food', 'wine', 'culinary', 'tasting'],
                'included' => ['Food samples', 'Wine tasting', 'Guide'],
                'excluded' => ['Full meals', 'Transportation'],
                'location_lat' => 48.8566,
                'location_lng' => 2.3522,
                'is_featured' => true,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Food expert', 'transportation' => 'Walking'],
            ],
            [
                'name_en' => 'Paris Night Tour',
                'name_ar' => 'جولة باريس الليلية',
                'slug' => 'paris-night-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 65.00,
                'child_price' => 40.00,
                'infant_price' => 0.00,
                'max_participants' => 25,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar', 'fr'],
                'tags' => ['night', 'illuminated', 'city lights'],
                'included' => ['Transportation', 'Guide'],
                'excluded' => ['Dinner', 'Drinks'],
                'location_lat' => 48.8566,
                'location_lng' => 2.3522,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Bus'],
            ],
            [
                'name_en' => 'Paris Montmartre Tour',
                'name_ar' => 'جولة مونمارتر في باريس',
                'slug' => 'paris-montmartre-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 40.00,
                'child_price' => 20.00,
                'infant_price' => 0.00,
                'max_participants' => 20,
                'min_participants' => 1,
                'difficulty' => 'moderate',
                'languages' => ['en', 'ar', 'fr'],
                'tags' => ['montmartre', 'sacré-cœur', 'artists'],
                'included' => ['Guide'],
                'excluded' => ['Transportation', 'Entrance fees'],
                'location_lat' => 48.8867,
                'location_lng' => 2.3431,
                'is_featured' => false,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Walking'],
            ],
            // Egypt Tours
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

                'provider_info' => ['guide_type' => 'Egyptologist', 'transportation' => 'Bus'],
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

                'provider_info' => ['guide_type' => 'Tour guide', 'transportation' => 'Boat'],
            ],
            [
                'name_en' => 'Valley of the Kings Tour',
                'name_ar' => 'جولة وادي الملوك',
                'slug' => 'valley-of-the-kings-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 120.00,
                'child_price' => 60.00,
                'infant_price' => 0.00,
                'max_participants' => 25,
                'min_participants' => 2,
                'difficulty' => 'moderate',
                'languages' => ['en', 'ar'],
                'tags' => ['historical', 'tombs', 'luxor'],
                'included' => ['Transportation', 'Guide', 'Entrance fees'],
                'excluded' => ['Lunch', 'Tips'],
                'location_lat' => 25.7400,
                'location_lng' => 32.6014,
                'is_featured' => true,
                'provider_info' => ['guide_type' => 'Egyptologist', 'transportation' => 'Bus'],
            ],
            // Dubai Tours
            [
                'name_en' => 'Dubai Desert Safari',
                'name_ar' => 'رحلة سفاري دبي الصحراوية',
                'slug' => 'dubai-desert-safari',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 80.00,
                'child_price' => 40.00,
                'infant_price' => 0.00,
                'max_participants' => 40,
                'min_participants' => 2,
                'difficulty' => 'moderate',
                'languages' => ['en', 'ar'],
                'tags' => ['desert', 'safari', 'adventure'],
                'included' => ['Transportation', 'Guide', 'BBQ dinner', 'Entertainment'],
                'excluded' => ['Drinks', 'Tips'],
                'location_lat' => 25.2048,
                'location_lng' => 55.2708,
                'is_featured' => true,
                'provider_info' => ['guide_type' => 'Adventure guide', 'transportation' => '4x4'],
            ],
            [
                'name_en' => 'Dubai City Tour',
                'name_ar' => 'جولة دبي المدينة',
                'slug' => 'dubai-city-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 70.00,
                'child_price' => 35.00,
                'infant_price' => 0.00,
                'max_participants' => 30,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar'],
                'tags' => ['city', 'burj khalifa', 'modern'],
                'included' => ['Transportation', 'Guide'],
                'excluded' => ['Entrance fees', 'Lunch'],
                'location_lat' => 25.2048,
                'location_lng' => 55.2708,
                'is_featured' => true,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Bus'],
            ],
            // More Egypt Tours
            [
                'name_en' => 'Cairo City Tour',
                'name_ar' => 'جولة القاهرة',
                'slug' => 'cairo-city-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 60.00,
                'child_price' => 30.00,
                'infant_price' => 0.00,
                'max_participants' => 25,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar'],
                'tags' => ['city', 'historical', 'cultural'],
                'included' => ['Transportation', 'Guide'],
                'excluded' => ['Entrance fees', 'Lunch'],
                'location_lat' => 30.0444,
                'location_lng' => 31.2357,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Bus'],
            ],
            [
                'name_en' => 'Alexandria Day Trip',
                'name_ar' => 'رحلة الإسكندرية اليومية',
                'slug' => 'alexandria-day-trip',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 90.00,
                'child_price' => 45.00,
                'infant_price' => 0.00,
                'max_participants' => 30,
                'min_participants' => 2,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar'],
                'tags' => ['alexandria', 'coastal', 'historical'],
                'included' => ['Transportation', 'Guide', 'Lunch'],
                'excluded' => ['Entrance fees'],
                'location_lat' => 31.2001,
                'location_lng' => 29.9187,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Bus'],
            ],
            [
                'name_en' => 'Sharm El Sheikh Snorkeling',
                'name_ar' => 'الغوص في شرم الشيخ',
                'slug' => 'sharm-el-sheikh-snorkeling',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 70.00,
                'child_price' => 35.00,
                'infant_price' => 0.00,
                'max_participants' => 20,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar'],
                'tags' => ['snorkeling', 'beach', 'water sports'],
                'included' => ['Equipment', 'Guide', 'Transportation'],
                'excluded' => ['Lunch'],
                'location_lat' => 27.9158,
                'location_lng' => 34.3299,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Dive instructor', 'transportation' => 'Boat'],
            ],
            [
                'name_en' => 'Luxor Temple Tour',
                'name_ar' => 'جولة معبد الأقصر',
                'slug' => 'luxor-temple-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 85.00,
                'child_price' => 42.50,
                'infant_price' => 0.00,
                'max_participants' => 25,
                'min_participants' => 2,
                'difficulty' => 'moderate',
                'languages' => ['en', 'ar'],
                'tags' => ['temple', 'historical', 'ancient'],
                'included' => ['Transportation', 'Guide', 'Entrance fees'],
                'excluded' => ['Lunch'],
                'location_lat' => 25.6872,
                'location_lng' => 32.6396,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Egyptologist', 'transportation' => 'Bus'],
            ],
            [
                'name_en' => 'Aswan Felucca Ride',
                'name_ar' => 'رحلة الفلوكة في أسوان',
                'slug' => 'aswan-felucca-ride',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 40.00,
                'child_price' => 20.00,
                'infant_price' => 0.00,
                'max_participants' => 15,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'ar'],
                'tags' => ['felucca', 'nile', 'traditional'],
                'included' => ['Boat ride', 'Guide'],
                'excluded' => ['Transportation', 'Lunch'],
                'location_lat' => 24.0889,
                'location_lng' => 32.8998,
                'is_featured' => false,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Boat'],
            ],
            // More International Tours
            [
                'name_en' => 'London City Tour',
                'name_ar' => 'جولة لندن',
                'slug' => 'london-city-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 55.00,
                'child_price' => 28.00,
                'infant_price' => 0.00,
                'max_participants' => 30,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en'],
                'tags' => ['london', 'city', 'historical'],
                'included' => ['Transportation', 'Guide'],
                'excluded' => ['Entrance fees', 'Lunch'],
                'location_lat' => 51.5074,
                'location_lng' => -0.1278,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Bus'],
            ],
            [
                'name_en' => 'Rome Colosseum Tour',
                'name_ar' => 'جولة الكولوسيوم في روما',
                'slug' => 'rome-colosseum-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 65.00,
                'child_price' => 32.50,
                'infant_price' => 0.00,
                'max_participants' => 25,
                'min_participants' => 1,
                'difficulty' => 'moderate',
                'languages' => ['en', 'it'],
                'tags' => ['colosseum', 'historical', 'ancient'],
                'included' => ['Transportation', 'Guide', 'Entrance fees'],
                'excluded' => ['Lunch'],
                'location_lat' => 41.9028,
                'location_lng' => 12.4964,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Historian', 'transportation' => 'Bus'],
            ],
            [
                'name_en' => 'Barcelona Gaudi Tour',
                'name_ar' => 'جولة غaudi في برشلونة',
                'slug' => 'barcelona-gaudi-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 60.00,
                'child_price' => 30.00,
                'infant_price' => 0.00,
                'max_participants' => 20,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'es'],
                'tags' => ['gaudi', 'architecture', 'art'],
                'included' => ['Transportation', 'Guide'],
                'excluded' => ['Entrance fees', 'Lunch'],
                'location_lat' => 41.3851,
                'location_lng' => 2.1734,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Art guide', 'transportation' => 'Bus'],
            ],
            [
                'name_en' => 'Istanbul Bosphorus Cruise',
                'name_ar' => 'رحلة البوسفور في إسطنبول',
                'slug' => 'istanbul-bosphorus-cruise',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 50.00,
                'child_price' => 25.00,
                'infant_price' => 0.00,
                'max_participants' => 40,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'tr'],
                'tags' => ['bosphorus', 'cruise', 'scenic'],
                'included' => ['Boat ride', 'Guide'],
                'excluded' => ['Transportation', 'Lunch'],
                'location_lat' => 41.0082,
                'location_lng' => 28.9784,
                'is_featured' => true,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Boat'],
            ],
            [
                'name_en' => 'Athens Acropolis Tour',
                'name_ar' => 'جولة الأكروبوليس في أثينا',
                'slug' => 'athens-acropolis-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 55.00,
                'child_price' => 27.50,
                'infant_price' => 0.00,
                'max_participants' => 25,
                'min_participants' => 1,
                'difficulty' => 'moderate',
                'languages' => ['en', 'el'],
                'tags' => ['acropolis', 'historical', 'ancient'],
                'included' => ['Transportation', 'Guide', 'Entrance fees'],
                'excluded' => ['Lunch'],
                'location_lat' => 37.9838,
                'location_lng' => 23.7275,
                'is_featured' => true,
                'transport_included' => true,
                'provider_info' => ['guide_type' => 'Historian', 'transportation' => 'Bus'],
            ],
            [
                'name_en' => 'Amsterdam Canal Tour',
                'name_ar' => 'جولة القنوات في أمستردام',
                'slug' => 'amsterdam-canal-tour',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 45.00,
                'child_price' => 22.50,
                'infant_price' => 0.00,
                'max_participants' => 30,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'nl'],
                'tags' => ['canal', 'scenic', 'city'],
                'included' => ['Boat ride', 'Guide'],
                'excluded' => ['Transportation', 'Lunch'],
                'location_lat' => 52.3676,
                'location_lng' => 4.9041,
                'is_featured' => true,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Local guide', 'transportation' => 'Boat'],
            ],
            [
                'name_en' => 'Venice Gondola Ride',
                'name_ar' => 'رحلة الجondola في البندقية',
                'slug' => 'venice-gondola-ride',
                'duration_days' => 1,
                'duration_nights' => 0,
                'adult_price' => 80.00,
                'child_price' => 40.00,
                'infant_price' => 0.00,
                'max_participants' => 6,
                'min_participants' => 1,
                'difficulty' => 'easy',
                'languages' => ['en', 'it'],
                'tags' => ['gondola', 'romantic', 'venice'],
                'included' => ['Gondola ride', 'Guide'],
                'excluded' => ['Transportation', 'Lunch'],
                'location_lat' => 45.4408,
                'location_lng' => 12.3155,
                'is_featured' => true,
                'transport_included' => false,
                'provider_info' => ['guide_type' => 'Gondolier', 'transportation' => 'Gondola'],
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
                    'transport_included' => $tourData['transport_included'] ?? true,
                    'provider_info' => $tourData['provider_info'] ?? null,
                    'rating' => rand(40, 50) / 10, // Random rating between 4.0 and 5.0
                    'total_reviews' => rand(10, 100),
                    'created_by' => $admin->id,
                ]
            );

            // Add translations
            $tour->translateOrNew('en')->name = $tourData['name_en'];
            $tour->translateOrNew('en')->short_description = "Experience {$tourData['name_en']}";
            $tour->translateOrNew('en')->description = "Full description for {$tourData['name_en']}";
            $tour->translateOrNew('en')->highlights = "Highlight 1\nHighlight 2\nHighlight 3";
            $tour->translateOrNew('en')->meeting_point = 'Hotel lobby';
            // Add translations with more detailed descriptions
            $highlightsEn = match (true) {
                str_contains(strtolower($tourData['name_en']), 'evening cruise') => "Evening cruise\nEiffel Tower view\nRomantic experience\nRiver Seine\nCity lights",
                str_contains(strtolower($tourData['name_en']), 'iconic') => "Iconic landmarks\nHidden gems\nEiffel Tower\nNotre Dame\nChamps-Élysées",
                str_contains(strtolower($tourData['name_en']), 'art') || str_contains(strtolower($tourData['name_en']), 'louvre') => "Museums\nArt galleries\nCultural sites\nLouvre Museum\nMona Lisa",
                str_contains(strtolower($tourData['name_en']), 'historical') => "Notre Dame\nLatin Quarter\nHistoric sites\nMedieval architecture\nCultural heritage",
                str_contains(strtolower($tourData['name_en']), 'food') => "Food tasting\nWine sampling\nLocal cuisine\nFrench delicacies\nCulinary experience",
                str_contains(strtolower($tourData['name_en']), 'night') => "Illuminated monuments\nCity lights\nEvening views\nEiffel Tower at night\nRomantic atmosphere",
                str_contains(strtolower($tourData['name_en']), 'montmartre') => "Montmartre district\nSacré-Cœur\nArtists square\nBohemian atmosphere\nHistoric streets",
                default => "Tour highlights\nAmazing experience\nProfessional guide\nMemorable journey",
            };

            $highlightsAr = match (true) {
                str_contains(strtolower($tourData['name_en']), 'evening cruise') => "رحلة مسائية\nمنظر برج إيفل\nتجربة رومانسية\nنهر السين\nأضواء المدينة",
                str_contains(strtolower($tourData['name_en']), 'iconic') => "معالم أيقونية\nجواهر مخفية\nبرج إيفل\nكاتدرائية نوتردام\nشانزليزيه",
                str_contains(strtolower($tourData['name_en']), 'art') || str_contains(strtolower($tourData['name_en']), 'louvre') => "متاحف\nمعارض فنية\nمواقع ثقافية\nمتحف اللوفر\nالموناليزا",
                str_contains(strtolower($tourData['name_en']), 'historical') => "نوتردام\nالحي اللاتيني\nمواقع تاريخية\nعمارة القرون الوسطى\nالتراث الثقافي",
                default => "ميزات الجولة\nتجربة رائعة\nدليل محترف\nرحلة لا تُنسى",
            };

            $shortDescEn = match (true) {
                str_contains(strtolower($tourData['name_en']), 'evening cruise') => "Enjoy a romantic evening cruise in Paris with stunning views of the Eiffel Tower and city lights reflecting on the Seine River.",
                str_contains(strtolower($tourData['name_en']), 'iconic') => "Explore Paris's iconic landmarks and hidden gems in this comprehensive city tour.",
                str_contains(strtolower($tourData['name_en']), 'art') => "Discover Paris's artistic side with visits to renowned museums and art galleries.",
                str_contains(strtolower($tourData['name_en']), 'historical') => "Explore the historic heart of Paris, including Notre Dame and the Latin Quarter.",
                str_contains(strtolower($tourData['name_en']), 'louvre') => "A guided tour of the Louvre Museum, showcasing Paris's art and cultural heritage.",
                str_contains(strtolower($tourData['name_en']), 'food') => "Experience the best of Parisian cuisine with food and wine tastings at local establishments.",
                str_contains(strtolower($tourData['name_en']), 'night') => "Discover Paris by night with illuminated monuments and stunning city lights.",
                str_contains(strtolower($tourData['name_en']), 'montmartre') => "Explore the bohemian Montmartre district, home to artists and the iconic Sacré-Cœur.",
                default => "Experience {$tourData['name_en']} with professional guides and unforgettable memories.",
            };

            $shortDescAr = match (true) {
                str_contains(strtolower($tourData['name_en']), 'evening cruise') => "استمتع برحلة مسائية رومانسية في باريس مع مناظر خلابة لبرج إيفل وأضواء المدينة المنعكسة على نهر السين.",
                str_contains(strtolower($tourData['name_en']), 'iconic') => "استكشف المعالم الأيقونية في باريس والجواهر المخفية في هذه الجولة الشاملة للمدينة.",
                str_contains(strtolower($tourData['name_en']), 'art') => "اكتشف الجانب الفني لباريس مع زيارة المتاحف الشهيرة ومعارض الفن.",
                str_contains(strtolower($tourData['name_en']), 'historical') => "استكشف القلب التاريخي لباريس، بما في ذلك نوتردام والحي اللاتيني.",
                str_contains(strtolower($tourData['name_en']), 'louvre') => "جولة إرشادية في متحف اللوفر، تعرض فن باريس وتراثها الثقافي.",
                default => "تجربة {$tourData['name_ar']} مع أدلة محترفة وذكريات لا تُنسى.",
            };

            $tour->translateOrNew('en')->name = $tourData['name_en'];
            $tour->translateOrNew('en')->short_description = $shortDescEn;
            $tour->translateOrNew('en')->description = "Full detailed description for {$tourData['name_en']}. This tour offers an amazing experience with professional guides, comfortable transportation, and unforgettable memories. Perfect for travelers looking to explore and discover new places.";
            $tour->translateOrNew('en')->highlights = $highlightsEn;
            $tour->translateOrNew('en')->meeting_point = $tourData['location_lat'] > 40 ? 'Hotel lobby' : 'City center';
>>>>>>> 6e876ba9d73195e746d0ed47df06f9269b0e177e
            $tour->translateOrNew('en')->cancellation_policy = 'Free cancellation up to 24 hours before tour';
            $tour->translateOrNew('en')->terms_conditions = 'Terms and conditions apply';

            $tour->translateOrNew('ar')->name = $tourData['name_ar'];
<<<<<<< HEAD
            $tour->translateOrNew('ar')->short_description = "تجربة {$tourData['name_ar']}";
            $tour->translateOrNew('ar')->description = "وصف كامل لـ {$tourData['name_ar']}";
            $tour->translateOrNew('ar')->highlights = "ميزة 1\nميزة 2\nميزة 3";
            $tour->translateOrNew('ar')->meeting_point = 'لوبي الفندق';
=======
            $tour->translateOrNew('ar')->short_description = $shortDescAr;
            $tour->translateOrNew('ar')->description = "وصف تفصيلي كامل لـ {$tourData['name_ar']}. تقدم هذه الجولة تجربة رائعة مع أدلة محترفة ووسائل نقل مريحة وذكريات لا تُنسى. مثالية للمسافرين الذين يبحثون عن الاستكشاف واكتشاف أماكن جديدة.";
            $tour->translateOrNew('ar')->highlights = $highlightsAr;
            $tour->translateOrNew('ar')->meeting_point = $tourData['location_lat'] > 40 ? 'لوبي الفندق' : 'وسط المدينة';
>>>>>>> 6e876ba9d73195e746d0ed47df06f9269b0e177e
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

<<<<<<< HEAD
                $itinerary->translateOrNew('en')->title = "Day {$day}";
                $itinerary->translateOrNew('en')->description = "Day {$day} description";
                $itinerary->translateOrNew('en')->location = "Location for day {$day}";
                $itinerary->translateOrNew('en')->duration = '8 hours';
=======
                // Set time-based duration for Paris tours
                $timeSlots = match (true) {
                    str_contains(strtolower($tourData['name_en']), 'evening cruise') => ['18:00', '21:00', '3 hours'],
                    str_contains(strtolower($tourData['name_en']), 'iconic') => ['10:00', '13:00', '3 hours'],
                    str_contains(strtolower($tourData['name_en']), 'art') => ['14:00', '17:00', '3 hours'],
                    str_contains(strtolower($tourData['name_en']), 'historical') => ['09:00', '12:00', '3 hours'],
                    str_contains(strtolower($tourData['name_en']), 'louvre') => ['13:00', '16:00', '3 hours'],
                    str_contains(strtolower($tourData['name_en']), 'food') => ['11:00', '15:00', '4 hours'],
                    str_contains(strtolower($tourData['name_en']), 'night') => ['19:00', '22:00', '3 hours'],
                    str_contains(strtolower($tourData['name_en']), 'montmartre') => ['10:00', '14:00', '4 hours'],
                    default => ['09:00', '17:00', '8 hours'],
                };

                $itinerary->translateOrNew('en')->title = "Day {$day}";
                $itinerary->translateOrNew('en')->description = "Day {$day} description with detailed itinerary";
                $itinerary->translateOrNew('en')->location = "Location for day {$day}";
                $itinerary->translateOrNew('en')->duration = "{$timeSlots[0]} - {$timeSlots[1]} ({$timeSlots[2]})";
>>>>>>> 6e876ba9d73195e746d0ed47df06f9269b0e177e

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
