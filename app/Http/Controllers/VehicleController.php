<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('owner:users_id,name')->get();
        
        return response()->json([
            'status' => 'success',
            'vehicles' => $vehicles
        ]);
    }

    public function store(Request $request)
    {
        // Check if user is an owner
        if ($request->user->role !== 'owners') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Owner access required.'
            ], 403);
        }

        $request->validate([
            'vehicles_name' => 'required|string',
            'plate_number' => 'required|string',
            'model' => 'required|string',
            'fuel_type' => 'required|string',
            'price_per_day' => 'required|string',
            'location' => 'required|string'
        ]);

        $vehicle = Vehicle::create([
            'users_id' => $request->user->users_id,
            'vehicles_name' => $request->vehicles_name,
            'plate_number' => $request->plate_number,
            'model' => $request->model,
            'fuel_type' => $request->fuel_type,
            'price_per_day' => $request->price_per_day,
            'location' => $request->location
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle added successfully',
            'vehicle' => $vehicle
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        
        if (!$vehicle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vehicle not found'
            ], 404);
        }

        // Check if user is the owner of this vehicle
        if ($request->user->role !== 'owners' || $vehicle->users_id !== $request->user->users_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the owner can update this vehicle.'
            ], 403);
        }

        $request->validate([
            'vehicles_name' => 'sometimes|string',
            'plate_number' => 'sometimes|string',
            'model' => 'sometimes|string',
            'fuel_type' => 'sometimes|string',
            'price_per_day' => 'sometimes|string',
            'location' => 'sometimes|string'
        ]);

        // Update fields
        $vehicle->fill($request->only([
            'vehicles_name', 
            'plate_number', 
            'model', 
            'fuel_type', 
            'price_per_day', 
            'location'
        ]));
        
        $vehicle->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle updated successfully',
            'vehicle' => $vehicle
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        
        if (!$vehicle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vehicle not found'
            ], 404);
        }

        // Check if user is the owner of this vehicle
        if ($request->user->role !== 'owners' || $vehicle->users_id !== $request->user->users_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the owner can delete this vehicle.'
            ], 403);
        }

        $vehicle->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle deleted successfully'
        ]);
    }
}