<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user:users_id,name', 'vehicle'])->get();
        
        return response()->json([
            'status' => 'success',
            'reviews' => $reviews
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
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        // Verify that the user has actually booked this vehicle before
        $hasBooked = Booking::where('users_id', $request->user->users_id)
                          ->where('vehicles_id', $request->vehicles_id)
                          ->where('status', 'confirmed')
                          ->exists();
        
        if (!$hasBooked) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can only review vehicles that you have booked and used.'
            ], 403);
        }

        // Check if user has already reviewed this vehicle
        $existingReview = Review::where('users_id', $request->user->users_id)
                              ->where('vehicles_id', $request->vehicles_id)
                              ->first();
        
        if ($existingReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already reviewed this vehicle. You can update your review instead.'
            ], 400);
        }

        $review = Review::create([
            'users_id' => $request->user->users_id,
            'vehicles_id' => $request->vehicles_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Review added successfully',
            'review' => $review
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $review = Review::find($id);
        
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user is the one who made this review
        if ($request->user->role !== 'renters' || $review->users_id !== $request->user->users_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the reviewer can update this review.'
            ], 403);
        }

        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        if ($request->has('rating')) {
            $review->rating = $request->rating;
        }
        
        if ($request->has('comment')) {
            $review->comment = $request->comment;
        }
        
        $review->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Review updated successfully',
            'review' => $review
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $review = Review::find($id);
        
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user is the one who made this review
        if ($request->user->role !== 'renters' || $review->users_id !== $request->user->users_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the reviewer can delete this review.'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully'
        ]);
    }
}