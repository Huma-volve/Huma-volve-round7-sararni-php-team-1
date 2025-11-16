<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Question\AnswerQuestionRequest;
use App\Http\Requests\Api\V1\Question\AskQuestionRequest;
use App\Models\Question;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    public function index(int $tourId): JsonResponse
    {
        $questions = Question::query()
            ->with(['user', 'answeredBy'])
            ->where('tour_id', $tourId)
            ->answered()
            ->latest()
            ->paginate(10);

        $data = $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'answer' => $question->answer,
                'user' => [
                    'id' => $question->user->id,
                    'name' => $question->user->name,
                ],
                'answered_by' => $question->answeredBy ? [
                    'id' => $question->answeredBy->id,
                    'name' => $question->answeredBy->name,
                ] : null,
                'answered_at' => $question->answered_at?->toIso8601String(),
                'created_at' => $question->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $questions->currentPage(),
                'per_page' => $questions->perPage(),
                'total' => $questions->total(),
                'last_page' => $questions->lastPage(),
            ],
        ]);
    }

    public function store(AskQuestionRequest $request): JsonResponse
    {
        Tour::findOrFail($request->tour_id);

        $question = Question::create([
            'user_id' => $request->user()->id,
            'tour_id' => $request->tour_id,
            'status' => 'pending',
        ]);

        $question->translateOrNew(app()->getLocale())->question = $request->question;
        $question->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.question.created'),
            'data' => [
                'id' => $question->id,
                'question' => $question->question,
                'status' => $question->status,
                'created_at' => $question->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    public function answer(AnswerQuestionRequest $request, int $id): JsonResponse
    {
        $question = Question::findOrFail($id);
        $question->answer($request->user()->id, $request->answer);

        return response()->json([
            'success' => true,
            'message' => __('messages.question.answered'),
            'data' => [
                'id' => $question->id,
                'question' => $question->question,
                'answer' => $question->answer,
                'status' => $question->status,
                'answered_at' => $question->answered_at?->toIso8601String(),
            ],
        ]);
    }
}
