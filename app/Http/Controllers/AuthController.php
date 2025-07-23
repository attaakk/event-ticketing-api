<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'name' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required',
                'role' => 'required|in:attendee,organizer'
            ]);
            $fields['password'] = Hash::make($fields['password']);
            $user = User::create($fields);

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'statusCode' => 201,
                'data' => $user,
                'errors' => null
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'statusCode' => 422,
                'data' => null,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $fields = $request->validate([
                'email' => 'required',
                'password' => 'required'
            ]);
            $user = User::where('email', $fields['email'])->first();
            if (!$user || !Hash::check($fields['password'], $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials',
                    'statusCode' => 401,
                    'data' => null,
                    'errors' => null
                ], 401);
            }
            $token = $user->createToken('api_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'statusCode' => 200,
                'data' => ['token' => $token],
                'errors' => null
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'statusCode' => 422,
                'data' => null,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();
            return response()->json([
                'status' => 'success',
                'message' => 'Profile fetched',
                'statusCode' => 200,
                'data' => $user,
                'errors' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch profile',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
