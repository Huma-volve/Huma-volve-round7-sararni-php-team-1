<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Collection;

class TourService
{
    public function getSimilarTours(Tour $tour, int $limit = 6): Collection
    {
        $query = Tour::query()
            ->where('id', '!=', $tour->id)
            ->where('status', 'active')
            ->where('category_id', $tour->category_id);

        // Match by tags if available
        if ($tour->tags && is_array($tour->tags) && count($tour->tags) > 0) {
            $query->where(function ($q) use ($tour) {
                foreach ($tour->tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        // Match by location if available
        if ($tour->location_lat && $tour->location_lng) {
            $query->whereNotNull('location_lat')
                ->whereNotNull('location_lng')
                ->whereRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(location_lat)) * cos(radians(location_lng) - radians(?)) + sin(radians(?)) * sin(radians(location_lat)))) <= ?',
                    [$tour->location_lat, $tour->location_lng, $tour->location_lat, 50]
                );
        }

        return $query->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecommendedTours($user = null, int $limit = 6): Collection
    {
        $query = Tour::query()
            ->where('status', 'active');

        if ($user) {
            // TODO: Implement user-based recommendations
            // Based on booking history, favorites, preferences
            // For now, return featured tours
            $query->where('is_featured', true);
        } else {
            // For guests, return featured tours with high ratings
            $query->where('is_featured', true);
        }

        return $query->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->orderBy('total_bookings', 'desc')
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
                $q->where('date', $filters['date'])
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
}
