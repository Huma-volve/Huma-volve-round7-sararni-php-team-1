<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Tour\CompareTourRequest;
use App\Http\Requests\Api\V1\Tour\IndexTourRequest;
use App\Http\Requests\Api\V1\Tour\SearchTourRequest;
use App\Http\Resources\Api\V1\TourCompareResource;
use App\Http\Resources\Api\V1\TourDetailResource;
use App\Http\Resources\Api\V1\TourResource;
use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function __construct(
        protected TourService $tourService
    ) {}

    public function index(IndexTourRequest $request): JsonResponse
    {
        $query = Tour::query()
            ->with(['category', 'media'])
            ->active();

        // Eager load user's favorites if authenticated to avoid N+1 queries
        if ($request->user()) {
            $userFavorites = \App\Models\Favorite::where('user_id', $request->user()->id)
                ->where('category', 'tour')
                ->pluck('item_id')
                ->toArray();

            // Add user favorites as a custom attribute to avoid N+1
            $query->withCount([
                'favorites as is_user_favorited' => function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                },
            ]);
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('price_min')) {
            $query->where('adult_price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('adult_price', '<=', $request->price_max);
        }

        if ($request->filled('rating_min')) {
            $query->where('rating', '>=', $request->rating_min);
        }

        if ($request->filled('date')) {
            $query->available($request->date);
        }

        if ($request->filled('location_lat') && $request->filled('location_lng')) {
            $query->byLocation($request->location_lat, $request->location_lng);
        }

        if ($request->filled('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        if ($request->filled('languages')) {
            $languages = $request->input('languages');
            $query->where(function ($q) use ($languages) {
                foreach ($languages as $language) {
                    $q->orWhereJsonContains('languages', $language);
                }
            });
        }

        if ($request->filled('tags')) {
            $tags = $request->input('tags');
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        if ($request->filled('featured')) {
            $query->featured();
        }

        $pageSize = $request->input('page_size', 20);
        $tours = $query->orderBy('sort_order')
            ->orderBy('rating', 'desc')
            ->paginate($pageSize);

        $tours->setCollection(TourResource::collection($tours->items())->collection);

        return $this->paginatedResponse($tours);
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $tour = Tour::with([
            'category',
            'itineraries',
            'activities',
            'reviews' => function ($query) {
                $query->approved()->with('user')->latest()->limit(10);
            },
            'questions' => function ($query) {
                $query->answered()->with(['user', 'answeredBy'])->latest()->limit(10);
            },
            'availability' => function ($query) {
                $query->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots')
                    ->orderBy('date')
                    ->limit(30);
            },
        ])->findOrFail($id);

        // Eager load user's favorite status if authenticated
        if ($request->user()) {
            $tour->loadCount([
                'favorites as is_user_favorited' => function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                },
            ]);
        }

        // Get similar tours
        $similarTours = $this->tourService->getSimilarTours($tour, 6);
        $tour->setRelation('similarTours', $similarTours);

        return $this->successResponse(new TourDetailResource($tour));
    }

    public function similar(int $id, Request $request): JsonResponse
    {
        $tour = Tour::findOrFail($id);
        $similarTours = $this->tourService->getSimilarTours($tour, 6);

        // Eager load user's favorites if authenticated
        if ($request->user() && $similarTours->isNotEmpty()) {
            $tourIds = $similarTours->pluck('id')->toArray();
            $favorites = \App\Models\Favorite::where('user_id', $request->user()->id)
                ->where('category', 'tour')
                ->whereIn('item_id', $tourIds)
                ->pluck('item_id')
                ->toArray();

            $similarTours->each(function ($tour) use ($favorites) {
                $tour->is_user_favorited = in_array($tour->id, $favorites) ? 1 : 0;
            });
        }

        return $this->successResponse(TourResource::collection($similarTours));
    }

    public function featured(Request $request): JsonResponse
    {
        $query = Tour::query()
            ->with(['category', 'media'])
            ->active()
            ->featured();

        // Eager load user's favorites if authenticated
        if ($request->user()) {
            $query->withCount([
                'favorites as is_user_favorited' => function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                },
            ]);
        }

        $tours = $query->orderBy('sort_order')
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();

        return $this->successResponse(TourResource::collection($tours));
    }

    public function search(SearchTourRequest $request): JsonResponse
    {
        $filters = $request->only([
            'category_id',
            'price_min',
            'price_max',
            'rating_min',
            'date',
            'location_lat',
            'location_lng',
            'difficulty',
            'languages',
            'tags',
            'featured',
        ]);

        $tours = $this->tourService->searchTours($request->q, $filters);

        // Eager load user's favorites if authenticated
        if ($request->user() && $tours->isNotEmpty()) {
            $tourIds = $tours->pluck('id')->toArray();
            $favorites = \App\Models\Favorite::where('user_id', $request->user()->id)
                ->where('category', 'tour')
                ->whereIn('item_id', $tourIds)
                ->pluck('item_id')
                ->toArray();

            $tours->each(function ($tour) use ($favorites) {
                $tour->is_user_favorited = in_array($tour->id, $favorites) ? 1 : 0;
            });
        }

        $pageSize = $request->input('page_size', 20);
        $page = $request->input('page', 1);

        $total = $tours->count();
        $lastPage = ceil($total / $pageSize);
        $paginated = $tours->forPage($page, $pageSize);
        $collection = TourResource::collection($paginated);

        // Create a custom paginator structure for meta
        return $this->successResponse(
            $collection,
            null,
            200
        )->withHeaders([
            'X-Current-Page' => (string) $page,
            'X-Per-Page' => (string) $pageSize,
            'X-Total' => (string) $total,
            'X-Last-Page' => (string) $lastPage,
        ]);
    }

    public function compare(CompareTourRequest $request): JsonResponse
    {
        $tourIds = $request->tour_ids;
        $date = $request->date;

        // Load tours with necessary relationships
        $tours = Tour::with([
            'availability' => function ($query) use ($date) {
                if ($date) {
                    $query->where('date', $date);
                }
                $query->where('is_active', true);
            },
            'media',
        ])
            ->whereIn('id', $tourIds)
            ->active()
            ->get();

        // Sort tours by the order of tour_ids in request
        $tours = $tours->sortBy(function ($tour) use ($tourIds) {
            return array_search($tour->id, $tourIds);
        })->values();

        // Pass date to resource for availability checking
        $request->merge(['date' => $date]);

        return $this->successResponse(
            TourCompareResource::collection($tours),
            'Tours compared successfully'
        );
    }
}
