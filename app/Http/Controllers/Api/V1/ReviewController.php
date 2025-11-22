<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Review\CreateReviewRequest;
use App\Http\Requests\Api\V1\Review\UpdateReviewRequest;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(int $tourId): JsonResponse
    {
        $reviews = Review::query()
            ->with('user')
            ->where('tour_id', $tourId)
            ->approved()
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
            ],
        ]);
    }

    public function store(CreateReviewRequest $request): JsonResponse
    {
        $booking = Booking::where('user_id', $request->user()->id)
            ->where('category', 'tour')
            ->where('item_id', $request->tour_id)
            ->where('id', $request->booking_id)
            ->firstOrFail();

        if ($booking->status !== 'completed') {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'BOOKING_NOT_COMPLETED',
                    'message' => __('messages.review.booking_not_completed'),
                ],
            ], 422);
        }

        // Check if review already exists
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('tour_id', $request->tour_id)
            ->where('booking_id', $request->booking_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'REVIEW_ALREADY_EXISTS',
                    'message' => __('messages.review.already_exists'),
                ],
            ], 422);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'tour_id' => $request->tour_id,
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'status' => 'pending',
        ]);

        $review->translateOrNew(app()->getLocale())->title = $request->title;
        $review->translateOrNew(app()->getLocale())->comment = $request->comment;
        $review->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.review.created'),
            'data' => new ReviewResource($review->load('user')),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $review = Review::with(['user', 'tour', 'booking'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (! $review) {
            return $this->errorResponse(
                'REVIEW_NOT_FOUND',
                __('messages.review.not_found'),
                null,
                404
            );
        }

        return $this->successResponse(new ReviewResource($review));
    }

    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        $review = Review::where('user_id', $request->user()->id)->find($id);

        if (! $review) {
            return $this->errorResponse(
                'REVIEW_NOT_FOUND',
                __('messages.review.not_found'),
                null,
                404
            );
        }

        if ($request->filled('rating')) {
            $review->rating = $request->rating;
        }

        $translation = $review->translateOrNew(app()->getLocale());

        if ($request->filled('title')) {
            $translation->title = $request->title;
        }

        if ($request->filled('comment')) {
            $translation->comment = $request->comment;
        }

        $review->save();
        $translation->save();

        return $this->successResponse(
            new ReviewResource($review->load('user')),
            __('messages.review.updated')
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $review = Review::where('user_id', $request->user()->id)->find($id);

        if (! $review) {
            return $this->errorResponse(
                'REVIEW_NOT_FOUND',
                __('messages.review.not_found'),
                null,
                404
            );
        }

        $tour = $review->tour;
        $review->delete();

        $tour->updateRating();

        return $this->successResponse(
            null,
            __('messages.review.deleted')
        );
    }
}
