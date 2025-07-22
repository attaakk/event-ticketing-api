<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\TicketType;

class TicketTypeController extends Controller
{
    public function store(Request $request, $eventId)
    {
        $user = $request->user();
        $event = Event::find($eventId);
        if (!$event || $event->organizer_id != $user->id || $user->role !== 'organizer') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized or event not found',
                'statusCode' => 403,
                'data' => null,
                'errors' => null
            ], 403);
        }
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'total_quantity' => 'required|integer'
        ]);
        $ticketType = TicketType::create([
            'event_id' => $event->id,
            'name' => $request->name,
            'price' => $request->price,
            'total_quantity' => $request->total_quantity,
            'available_quantity' => $request->total_quantity
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket type created',
            'statusCode' => 201,
            'data' => $ticketType,
            'errors' => null
        ], 201);
    }

    public function update(Request $request, $eventId, $typeId)
    {
        $user = $request->user();
        $ticketType = TicketType::where('event_id', $eventId)->find($typeId);
        $event = Event::find($eventId);
        if (!$ticketType || !$event || $event->organizer_id != $user->id || $user->role !== 'organizer') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized or ticket type not found',
                'statusCode' => 404,
                'data' => null,
                'errors' => null
            ], 404);
        }
        $ticketType->update($request->only(['name', 'price', 'total_quantity', 'available_quantity']));
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket type updated',
            'statusCode' => 200,
            'data' => $ticketType,
            'errors' => null
        ]);
    }

    public function destroy(Request $request, $eventId, $typeId)
    {
        $user = $request->user();
        $ticketType = TicketType::where('event_id', $eventId)->find($typeId);
        $event = Event::find($eventId);
        if (!$ticketType || !$event || $event->organizer_id != $user->id || $user->role !== 'organizer') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized or ticket type not found',
                'statusCode' => 404,
                'data' => null,
                'errors' => null
            ], 404);
        }
        $ticketType->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket type deleted',
            'statusCode' => 200,
            'data' => null,
            'errors' => null
        ]);
    }
}
