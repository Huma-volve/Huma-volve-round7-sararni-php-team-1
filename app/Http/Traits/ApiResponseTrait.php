<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait ApiResponseTrait
{
    /**
     * Return a successful JSON response.
     */
    protected function successResponse(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a successful JSON response with pagination metadata.
     */
    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        ?string $message = null,
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        // Get the collection from paginator (could be ResourceCollection)
        $items = $paginator->items();

        // If items are already ResourceCollection, use them as is
        // Otherwise, convert to array
        $response['data'] = $items;

        $response['meta'] = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Return a created JSON response (201).
     */
    protected function createdResponse(
        mixed $data = null,
        ?string $message = null
    ): JsonResponse {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(
        string $code,
        string $message,
        ?array $details = null,
        int $statusCode = 400
    ): JsonResponse {
        $response = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if ($details !== null) {
            $response['error']['details'] = $details;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a validation error JSON response (422).
     */
    protected function validationErrorResponse(
        array $errors,
        ?string $message = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => $message ?? __('validation.validation_failed'),
                'details' => $errors,
            ],
        ];

        return response()->json($response, 422);
    }

    /**
     * Return a not found JSON response (404).
     */
    protected function notFoundResponse(
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse(
            'NOT_FOUND',
            $message ?? __('messages.error.not_found'),
            null,
            404
        );
    }

    /**
     * Return an unauthorized JSON response (401).
     */
    protected function unauthorizedResponse(
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse(
            'UNAUTHORIZED',
            $message ?? __('messages.error.unauthorized'),
            null,
            401
        );
    }

    /**
     * Return a forbidden JSON response (403).
     */
    protected function forbiddenResponse(
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse(
            'FORBIDDEN',
            $message ?? __('messages.error.forbidden'),
            null,
            403
        );
    }

    /**
     * Return a too many requests JSON response (429).
     */
    protected function tooManyRequestsResponse(
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse(
            'TOO_MANY_ATTEMPTS',
            $message ?? __('messages.error.too_many_attempts'),
            null,
            429
        );
    }
}
