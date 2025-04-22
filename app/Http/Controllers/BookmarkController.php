<?php

// app/Http/Controllers/BookmarkController.php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        // Get all bookmarks for the current user
        $bookmarks = Bookmark::where('users_id', $request->user->users_id)
                          ->with('vehicle')
                          ->get();
        
        return response()->json([
            'status' => 'success',
            'bookmarks' => $bookmarks
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
            'vehicles_id' => 'required|exists:vehicles,vehicles_id'
        ]);

        // Check if bookmark already exists
        $existingBookmark = Bookmark::where('users_id', $request->user->users_id)
                                  ->where('vehicles_id', $request->vehicles_id)
                                  ->first();
        
        if ($existingBookmark) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vehicle is already bookmarked'
            ], 400);
        }

        $bookmark = Bookmark::create([
            'users_id' => $request->user->users_id,
            'vehicles_id' => $request->vehicles_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle bookmarked successfully',
            'bookmark' => $bookmark
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $bookmark = Bookmark::find($id);
        
        if (!$bookmark) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bookmark not found'
            ], 404);
        }

        // Check if user is the one who made this bookmark
        if ($request->user->role !== 'renters' || $bookmark->users_id !== $request->user->users_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the bookmark owner can remove it.'
            ], 403);
        }

        $bookmark->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bookmark removed successfully'
        ]);
    }
}