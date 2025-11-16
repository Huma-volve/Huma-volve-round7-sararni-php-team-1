<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TourResource;
use App\Models\Favorite;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favorites = Favorite::query()
            ->with(['tour.category', 'tour.media'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => TourResource::collection($favorites->pluck('tour')),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
                'last_page' => $favorites->lastPage(),
            ],
        ]);
    }

    public function toggle(Request $request, int $tourId): JsonResponse
    {
        Tour::findOrFail($tourId);

        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('tour_id', $tourId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $request->user()->id,
                'tour_id' => $tourId,
            ]);
            $isFavorited = true;
        }

        return response()->json([
            'success' => true,
            'message' => $isFavorited ? __('messages.favorite.added') : __('messages.favorite.removed'),
            'data' => [
                'is_favorited' => $isFavorited,
            ],
        ]);
    }

    public function check(Request $request, int $tourId): JsonResponse
    {
        $isFavorited = Favorite::where('user_id', $request->user()->id)
            ->where('tour_id', $tourId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_favorited' => $isFavorited,
            ],
        ]);
    }
}
