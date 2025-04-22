<?php

// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is admin
        if ($request->user->role !== 'admins') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $bookings = Booking::with(['user:users_id,name,email', 'vehicle'])->get();
        
        return response()->json([
            'status' => 'success',
            'bookings' => $bookings
        ]);
    }

    public function store(Request $request)
    {
        // Check if user is a renter
        if ($request->user->role !== 'renters') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Renter access required.'
            ], 403);
        }

        $request->validate([
            'vehicles_id' => 'required|exists:vehicles,vehicles_id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:pickup_date'
        ]);

        // Get vehicle to calculate total price
        $vehicle = Vehicle::find($request->vehicles_id);
        
        // Calculate number of days
        $pickupDate = new \DateTime($request->pickup_date);
        $returnDate = new \DateTime($request->return_date);
        $interval = $pickupDate->diff($returnDate);
        $days = $interval->days;
        
        // Calculate total price
        $totalPrice = $days * intval($vehicle->price_per_day);

        $booking = Booking::create([
            'users_id' => $request->user->users_id,
            'vehicles_id' => $request->vehicles_id,
            'pickup_date' => $request->pickup_date,
            'return_date' => $request->return_date,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully',
            'booking' => $booking
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::with('vehicle')->find($id);
        
        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }

        // Get the vehicle owner
        $vehicle = Vehicle::find($booking->vehicles_id);
        
        // Check if user is the owner of the booked vehicle
        if ($request->user->role !== 'owners' || $vehicle->users_id !== $request->user->users_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the vehicle owner can update this booking status.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,canceled'
        ]);

        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Booking status updated successfully',
            'booking' => $booking
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $booking = Booking::find($id);
        
        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }

        // Check if user is the renter who made this booking
        if ($request->user->role !== 'renters' || $booking->users_id !== $request->user->users_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the renter who made this booking can cancel it.'
            ], 403);
        }

        $booking->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Booking canceled successfully'
        ]);
    }
}