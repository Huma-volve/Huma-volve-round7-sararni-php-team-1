<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TourResource;
use App\Models\Favorite;
use App\Models\Tour;
use App\Models\Hotel;
use App\Models\Car;
use App\Models\Flight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $category = $request->input('category'); // Optional filter by category

        $query = Favorite::query()
            ->where('user_id', $request->user()->id);

        if ($category) {
            $query->where('category', $category);
        }

        $favorites = $query->latest()->paginate(20);

        // Group favorites by category and load items efficiently
        $itemsByCategory = $favorites->groupBy('category');
        $items = collect();

        foreach ($itemsByCategory as $cat => $categoryFavorites) {
            $itemIds = $categoryFavorites->pluck('item_id')->toArray();

            $loadedItems = match ($cat) {
                'tour' => Tour::whereIn('id', $itemIds)->with(['category', 'media'])->get()->keyBy('id'),
                'hotel' => Hotel::whereIn('id', $itemIds)->with(['location'])->get()->keyBy('id'),
                'car' => Car::whereIn('id', $itemIds)->with(['brand', 'pickupLocation', 'dropoffLocation'])->get()->keyBy('id'),
                'flight' => Flight::whereIn('id', $itemIds)->with(['carrier', 'aircraft', 'origin', 'destination'])->get()->keyBy('id'),
                default => collect(),
            };

            foreach ($categoryFavorites as $favorite) {
                $item = $loadedItems->get($favorite->item_id);
                if ($item) {
                    $items->push($item);
                }
            }
        }

        return $this->successResponse(
            $items->map(function ($item) {
                // Format based on category
                if ($item instanceof Tour) {
                    return new TourResource($item);
                }
                // TODO: Add HotelResource, CarResource, FlightResource
                return [
                    'id' => $item->id,
                    'category' => $this->getCategoryFromModel($item),
                    'name' => $item->name ?? $item->slug ?? $item->flight_number ?? null,
                ];
            }),
            null,
            200
        )->withHeaders([
            'X-Current-Page' => (string) $favorites->currentPage(),
            'X-Per-Page' => (string) $favorites->perPage(),
            'X-Total' => (string) $favorites->total(),
            'X-Last-Page' => (string) $favorites->lastPage(),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'category' => ['required', 'string', 'in:tour,hotel,car,flight'],
            'item_id' => ['required', 'integer'],
        ]);

        $category = $request->input('category');
        $itemId = $request->input('item_id');

        // Verify item exists
        $this->verifyItemExists($category, $itemId);

        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('category', $category)
            ->where('item_id', $itemId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $request->user()->id,
                'category' => $category,
                'item_id' => $itemId,
            ]);
            $isFavorited = true;
        }

        return $this->successResponse([
            'is_favorited' => $isFavorited,
        ], $isFavorited ? __('messages.favorite.added') : __('messages.favorite.removed'));
    }

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'category' => ['required', 'string', 'in:tour,hotel,car,flight'],
            'item_id' => ['required', 'integer'],
        ]);

        $isFavorited = Favorite::where('user_id', $request->user()->id)
            ->where('category', $request->input('category'))
            ->where('item_id', $request->input('item_id'))
            ->exists();

        return $this->successResponse([
            'is_favorited' => $isFavorited,
        ]);
    }

    /**
     * Verify that the item exists in the specified category
     */
    protected function verifyItemExists(string $category, int $itemId): void
    {
        match ($category) {
            'tour' => Tour::findOrFail($itemId),
            'hotel' => Hotel::findOrFail($itemId),
            'car' => Car::findOrFail($itemId),
            'flight' => Flight::findOrFail($itemId),
            default => throw new \InvalidArgumentException("Invalid category: {$category}"),
        };
    }

    /**
     * Get category from model instance
     */
    protected function getCategoryFromModel($model): string
    {
        return match (get_class($model)) {
            Tour::class => 'tour',
            Hotel::class => 'hotel',
            Car::class => 'car',
            Flight::class => 'flight',
            default => 'unknown',
        };
    }
}
