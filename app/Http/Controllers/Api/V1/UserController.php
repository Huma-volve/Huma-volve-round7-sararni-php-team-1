<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UpdateProfileRequest;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->load(['roles', 'socialIdentities'])),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $updateData = $request->only(['name', 'phone', 'location']);

        // Handle email change - requires re-verification
        if ($request->has('email') && $request->email !== $user->email) {
            $updateData['email'] = $request->email;
            $updateData['is_verified'] = false;
            $updateData['email_verified_at'] = null;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => __('messages.profile.updated'),
            'data' => new UserResource($user->load(['roles', 'socialIdentities'])),
        ]);
    }
}
