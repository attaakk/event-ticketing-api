<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Exception;

class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Event::where('status', 'published');
            if ($request->has('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }
            if ($request->has('date')) {
                $query->whereDate('start_time', $request->date);
            }
            $events = $query->with('ticketTypes')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Events fetched',
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

    public function show($eventId)
    {
        try {
            $event = Event::with('ticketTypes')->find($eventId);
            if (!$event || $event->status !== 'published') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event not found',
                    'statusCode' => 404,
                    'data' => null,
                    'errors' => null
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Event detail',
                'statusCode' => 200,
                'data' => $event,
                'errors' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch event detail',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->role !== 'organizer') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'statusCode' => 403,
                    'data' => null,
                    'errors' => null
                ], 403);
            }
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'venue' => 'required',
                'start_time' => 'required|date',
                'end_time' => 'required|date'
            ]);
            $event = Event::create([
                'organizer_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'venue' => $request->venue,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'draft'
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Event created',
                'statusCode' => 201,
                'data' => $event,
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
                'message' => 'Failed to create event',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $eventId)
    {
        try {
            $user = $request->user();
            $event = Event::find($eventId);
            if (!$event || $event->organizer_id != $user->id || $user->role !== 'organizer') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event not found or unauthorized',
                    'statusCode' => 404,
                    'data' => null,
                    'errors' => null
                ], 404);
            }
            $event->update($request->only(['title', 'description', 'venue', 'start_time', 'end_time', 'status']));
            return response()->json([
                'status' => 'success',
                'message' => 'Event updated',
                'statusCode' => 200,
                'data' => $event,
                'errors' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update event',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $eventId)
    {
        try {
            $user = $request->user();
            $event = Event::find($eventId);
            if (!$event || $event->organizer_id != $user->id || $user->role !== 'organizer') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event not found or unauthorized',
                    'statusCode' => 404,
                    'data' => null,
                    'errors' => null
                ], 404);
            }
            $event->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Event deleted',
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
