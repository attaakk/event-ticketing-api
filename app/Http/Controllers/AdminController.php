<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class AdminController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'statusCode' => 403,
                    'data' => null,
                    'errors' => null
                ], 403);
            }
            $request->validate([
                'name' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required'
            ]);
            $newAdmin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin'
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Admin created',
                'statusCode' => 201,
                'data' => $newAdmin,
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
                'message' => 'Failed to create admin',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function events(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'statusCode' => 403,
                    'data' => null,
                    'errors' => null
                ], 403);
            }
            $events = Event::with('ticketTypes')->get();
            return response()->json([
                'status' => 'success',
                'message' => 'All events',
                'statusCode' => 200,
                'data' => $events,
                'errors' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch events',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/admin/events/{eventId}/delete
    public function destroyEvent(Request $request, $eventId)
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'statusCode' => 403,
                    'data' => null,
                    'errors' => null
                ], 403);
            }
            $event = Event::find($eventId);
            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event not found',
                    'statusCode' => 404,
                    'data' => null,
                    'errors' => null
                ], 404);
            }
            $event->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Event force deleted',
                'statusCode' => 200,
                'data' => null,
                'errors' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete event',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
