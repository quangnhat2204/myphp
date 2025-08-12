<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class UserController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            $users = User::paginate(10);
            return UserResource::collection($users);
        } catch (Exception $e) {
            Log::error('Failed to retrieve users: ' . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not retrieve users.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            return new UserResource($user);
        } catch (ValidationException $e) {
            return $this->error(
                'The given data was invalid.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->errors()
            );
        } catch (Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not create user.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->update($validated);

            return new UserResource($user);
        } catch (ValidationException $e) {
            return $this->error(
                'The given data was invalid.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->errors()
            );
        } catch (Exception $e) {
            Log::error("Failed to update user {$user->id}: " . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not update user.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->noContent();
        } catch (Exception $e) {
            Log::error("Failed to delete user {$user->id}: " . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not delete user.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
