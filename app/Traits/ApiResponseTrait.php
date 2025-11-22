<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponseTrait
{
    protected function successResponse(JsonResource $resource, int $statusCode = 200)
    {

        $data = $resource->response()->getData(true);

        return response()->json([
            'status' => 'success',
            'data'   => $data['data'] ?? $data,
            'links'  => $data['links'] ?? null,
            'meta'   => $data['meta'] ?? null,
        ], $statusCode);
     }

    protected function errorResponse(string $message, int $statusCode = 400)
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
