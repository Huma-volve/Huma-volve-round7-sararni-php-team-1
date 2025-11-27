<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Search\SearchRequest;
use App\Http\Resources\Api\V1\TourResource;
use App\Http\Resources\HotelResource;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService
    ) {}

    /**
     * البحث الشامل في جميع الأنواع
     */
    public function search(SearchRequest $request): JsonResponse
    {
        $types = $request->input('types', ['tours']); // Default to tours only
        $perPage = $request->input('page_size', 20);

        $filters = $request->only([
            'category_id',
            'price_min',
            'price_max',
            'rating_min',
            'date',
            'location_lat',
            'location_lng',
            'radius',
            'difficulty',
            'languages',
            'tags',
            'featured',
            'stars',
            'brand_id',
            'location_id',
        ]);

        $results = $this->searchService->search($request->q, $filters, $types, $perPage);

        // Format results with pagination
        $formattedResults = [];
        foreach ($results as $type => $paginator) {
            if ($paginator && $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $formattedResults[$type] = [
                    'data' => $this->formatResultsByType($type, $paginator->items()),
                    'meta' => [
                        'current_page' => $paginator->currentPage(),
                        'per_page' => $paginator->perPage(),
                        'total' => $paginator->total(),
                        'last_page' => $paginator->lastPage(),
                    ],
                ];
            }
        }

        return $this->successResponse($formattedResults);
    }

    /**
     * البحث السريع (Quick Search)
     */
    public function quickSearch(SearchRequest $request): JsonResponse
    {
        $lat = $request->input('location_lat');
        $lng = $request->input('location_lng');
        $radius = $request->input('radius', 50);
        $limit = $request->input('limit', 10);

        $results = $this->searchService->quickSearch($request->q, $lat, $lng, $radius, $limit);

        // Format results
        $formattedResults = [];
        foreach ($results as $type => $collection) {
            if ($collection && is_iterable($collection)) {
                $formattedResults[$type] = $this->formatResultsByType($type, is_array($collection) ? $collection : $collection->toArray());
            }
        }

        return $this->successResponse($formattedResults);
    }

    /**
     * البحث بالقرب من موقع معين (Nearby Search)
     */
    public function nearby(SearchRequest $request): JsonResponse
    {
        $request->validate([
            'location_lat' => ['required', 'numeric'],
            'location_lng' => ['required', 'numeric'],
        ]);

        $types = $request->input('types', ['tours', 'hotels']);
        $radius = $request->input('radius', 10);
        $limit = $request->input('limit', 20);

        $results = $this->searchService->searchNearby(
            $request->location_lat,
            $request->location_lng,
            $types,
            $radius,
            $limit
        );

        // Format results
        $formattedResults = [];
        foreach ($results as $type => $collection) {
            if ($collection && is_iterable($collection)) {
                $formattedResults[$type] = $this->formatResultsByType($type, is_array($collection) ? $collection : $collection->toArray());
            }
        }

        return $this->successResponse($formattedResults);
    }

    /**
     * تنسيق النتائج حسب النوع
     */
    protected function formatResultsByType(string $type, array $items): array
    {
        return match ($type) {
            'tours' => TourResource::collection(collect($items))->resolve(),
            // TODO: إضافة Resources للأنواع الأخرى
            'hotels' => HotelResource::collection(collect($items))->resolve(),
            // 'cars' => CarResource::collection(collect($items))->resolve(),
            // 'flights' => FlightResource::collection(collect($items))->resolve(),
            default => $items,
        };
    }
}
