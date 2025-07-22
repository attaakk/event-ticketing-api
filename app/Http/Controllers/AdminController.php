<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function store(Request $request)
    {
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
    }

    public function events(Request $request)
    {
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
    }

    // DELETE /api/admin/events/{eventId}/delete
    public function destroyEvent(Request $request, $eventId)
    {
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
    }
}
