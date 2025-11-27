<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TourService
{
    public function getSimilarTours(Tour $tour, int $limit = 6): Collection
    {
        $baseQuery = Tour::query()
            ->where('id', '!=', $tour->id)
            ->where('status', 'active')
            ->where('category_id', $tour->category_id);

        $similarTours = (clone $baseQuery);
        $this->applySimilarityFilters($similarTours, $tour);

        $results = $similarTours->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit)
            ->get();

        if ($results->isNotEmpty()) {
            return $results;
        }

        // Fallback to category only
        $categoryFallback = (clone $baseQuery)->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit)
            ->get();

        if ($categoryFallback->isNotEmpty()) {
            return $categoryFallback;
        }

        // Final fallback: any active tours
        return Tour::query()
            ->where('id', '!=', $tour->id)
            ->where('status', 'active')
            ->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecommendedTours(array $filters = [], $user = null, int $limit = 6): Collection
    {
        $query = Tour::query()
            ->where('status', 'active');

        if ($user) {
            // TODO: Implement user-based recommendations
            // Based on booking history, favorites, preferences
            $query->where('is_featured', true);
        } else {
            // For guests, return featured tours with high ratings
            $query->where('is_featured', true);
        }

        $this->applyCommonFilters($query, $filters);

        return $query->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->orderBy('total_bookings', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAvailableTours(string $date, array $filters = [], int $limit = 20): Collection
    {
        $query = Tour::query()
            ->where('status', 'active')
            ->whereHas('availability', function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots');
            })
            ->with(['availability' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->where('is_active', true);
            }]);

        $this->applyCommonFilters($query, $filters);

        return $query->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit)
            ->get();
    }

    public function searchTours(string $query, array $filters = []): Collection
    {
        $searchQuery = Tour::query()
            ->where('status', 'active')
            ->whereHas('translations', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%")
                    ->orWhere('highlights', 'like', "%{$query}%");
            });

        // Apply filters
        if (isset($filters['category_id'])) {
            $searchQuery->where('category_id', $filters['category_id']);
        }

        if (isset($filters['price_min'])) {
            $searchQuery->where('adult_price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $searchQuery->where('adult_price', '<=', $filters['price_max']);
        }

        if (isset($filters['rating_min'])) {
            $searchQuery->where('rating', '>=', $filters['rating_min']);
        }

        if (isset($filters['date'])) {
            $searchQuery->whereHas('availability', function ($q) use ($filters) {
                $q->whereDate('date', $filters['date'])
                    ->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots');
            });
        }

        if (isset($filters['location_lat']) && isset($filters['location_lng'])) {
            $radius = $filters['radius'] ?? 50;
            $searchQuery->byLocation($filters['location_lat'], $filters['location_lng'], $radius);
        }

        if (isset($filters['difficulty'])) {
            $searchQuery->where('difficulty', $filters['difficulty']);
        }

        if (isset($filters['languages']) && is_array($filters['languages'])) {
            $searchQuery->where(function ($q) use ($filters) {
                foreach ($filters['languages'] as $language) {
                    $q->orWhereJsonContains('languages', $language);
                }
            });
        }

        if (isset($filters['tags']) && is_array($filters['tags'])) {
            $searchQuery->where(function ($q) use ($filters) {
                foreach ($filters['tags'] as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        if (isset($filters['featured'])) {
            $searchQuery->where('is_featured', $filters['featured']);
        }

        return $searchQuery->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->get();
    }

    protected function applyCommonFilters($query, array $filters): void
    {
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['price_min'])) {
            $query->where('adult_price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('adult_price', '<=', $filters['price_max']);
        }

        if (isset($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (isset($filters['languages']) && is_array($filters['languages'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['languages'] as $language) {
                    $q->orWhereJsonContains('languages', $language);
                }
            });
        }

        if (isset($filters['tags']) && is_array($filters['tags'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['tags'] as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        if (isset($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        if (isset($filters['location_lat']) && isset($filters['location_lng'])) {
            $radius = $filters['radius'] ?? 50;
            $query->byLocation($filters['location_lat'], $filters['location_lng'], $radius);
        }
    }

    protected function applySimilarityFilters(Builder $query, Tour $tour): void
    {
        if ($tour->tags && is_array($tour->tags)) {
            $tags = array_filter($tour->tags);

            if (! empty($tags)) {
                $limitedTags = array_slice(array_values($tags), 0, 3);
                $query->where(function ($q) use ($limitedTags) {
                    foreach ($limitedTags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                });
            }
        }

        if ($tour->location_lat && $tour->location_lng) {
            $driver = config('database.default');
            $connection = config("database.connections.{$driver}.driver");
            if ($connection !== 'sqlite') {
                $query->whereNotNull('location_lat')
                    ->whereNotNull('location_lng')
                    ->whereRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(location_lat)) * cos(radians(location_lng) - radians(?)) + sin(radians(?)) * sin(radians(location_lat)))) <= ?',
                        [$tour->location_lat, $tour->location_lng, $tour->location_lat, 100]
                    );
            }
        }
    }
}
