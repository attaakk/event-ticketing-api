<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketType;
use App\Models\IssuedTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'attendee') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'statusCode' => 403,
                'data' => null,
                'errors' => null
            ], 403);
        }
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1'
        ]);
        $ticketType = TicketType::find($request->ticket_type_id);
        if ($ticketType->available_quantity < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not enough ticket quota',
                'statusCode' => 400,
                'data' => null,
                'errors' => null
            ], 400);
        }
        DB::beginTransaction();
        try {
            $order = Order::create([
                'buyer_id' => $user->id,
                'total_amount' => $ticketType->price * $request->quantity
            ]);
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'ticket_type_id' => $ticketType->id,
                'quantity' => $request->quantity,
                'price_per_ticket' => $ticketType->price
            ]);
            for ($i = 0; $i < $request->quantity; $i++) {
                IssuedTicket::create([
                    'order_item_id' => $orderItem->id,
                    'owner_id' => $user->id,
                    'ticket_code' => (string) Str::uuid(),
                    'is_used' => false
                ]);
            }
            $ticketType->available_quantity -= $request->quantity;
            $ticketType->save();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Order created, tickets issued',
                'statusCode' => 201,
                'data' => $order,
                'errors' => null
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Order failed',
                'statusCode' => 500,
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function myOrders(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'attendee') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'statusCode' => 403,
                'data' => null,
                'errors' => null
            ], 403);
        }
        $orders = Order::where('buyer_id', $user->id)->with('orderItems')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Order history',
            'statusCode' => 200,
            'data' => $orders,
            'errors' => null
        ]);
    }

    public function myTickets(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'attendee') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'statusCode' => 403,
                'data' => null,
                'errors' => null
            ], 403);
        }
        $tickets = IssuedTicket::where('owner_id', $user->id)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'My tickets',
            'statusCode' => 200,
            'data' => $tickets,
            'errors' => null
        ]);
    }
}
