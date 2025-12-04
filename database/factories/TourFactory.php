<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    protected $model = Tour::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);
        $slugBase = Str::slug($name).'-'.Str::random(5);

        return [
            'category_id' => Category::factory(),
            'slug' => $slugBase,
            'duration_days' => $this->faker->numberBetween(1, 7),
            'duration_nights' => $this->faker->optional()->numberBetween(0, 6),
            'max_participants' => $this->faker->numberBetween(10, 30),
            'min_participants' => $this->faker->numberBetween(1, 5),
            'adult_price' => $this->faker->randomFloat(2, 50, 500),
            'child_price' => $this->faker->randomFloat(2, 20, 200),
            'infant_price' => $this->faker->randomFloat(2, 0, 50),
            'discount_percentage' => $this->faker->randomFloat(2, 0, 15),
            'status' => 'active',
            'rating' => $this->faker->randomFloat(2, 3, 5),
            'total_reviews' => $this->faker->numberBetween(10, 300),
            'total_bookings' => $this->faker->numberBetween(20, 500),
            'is_featured' => $this->faker->boolean(60),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'location_lat' => $this->faker->latitude(),
            'location_lng' => $this->faker->longitude(),
            'included' => ['guide', 'transport'],
            'excluded' => ['meals'],
            'languages' => ['en'],
            'difficulty' => $this->faker->randomElement(['easy', 'moderate', 'hard']),
            'provider_info' => ['guide_type' => 'Local guide'],
            'tags' => [$this->faker->word()],
            'transport_included' => $this->faker->boolean(),
            'pickup_zones' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Tour $tour) {
            $name = $this->faker->sentence(3);
            $tour->translateOrNew('en')->name = $name;
            $tour->translateOrNew('en')->short_description = $this->faker->sentence(8);
            $tour->translateOrNew('en')->description = $this->faker->paragraph();
            $tour->translateOrNew('en')->highlights = implode(', ', $this->faker->words(3));
            $tour->translateOrNew('en')->meeting_point = $this->faker->address();
            $tour->translateOrNew('en')->cancellation_policy = 'Flexible';
            $tour->translateOrNew('en')->terms_conditions = 'Standard terms apply';
            $tour->save();

            $tour->availability()->create([
                'date' => now()->addDay()->toDateString(),
                'available_slots' => 20,
                'booked_slots' => 2,
                'is_active' => true,
            ]);
        });
    }
}
