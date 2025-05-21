<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class FarmerController extends Controller
{
    /**
     * Display a listing of farmers.
     */
    public function index()
    {
        return view('admin.farmer_management');
    }

    /**
     * Store a newly created farmer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the farmer user (role_id = 1 for farmer)
        $farmer = User::create([
            'name' => $validated['name'],
            'username' => explode('@', $validated['email'])[0], // Generate username from email
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'location' => $validated['location'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id' => 1, // Farmer role
            'status' => 'active',
        ]);

        return redirect()->route('admin.farmers')->with('success', 'Farmer added successfully');
    }

    /**
     * Display the specified farmer.
     */
    public function show($id)
    {
        $farmer = User::findOrFail($id);

        if ($farmer->role_id != 1) {
            return response()->json(['error' => 'User is not a farmer'], 403);
        }

        return response()->json($farmer);
    }

    /**
     * Update the specified farmer in storage.
     */
    public function update(Request $request, $id)
    {
        $farmer = User::findOrFail($id);

        // Ensure we're updating a farmer
        if ($farmer->role_id != 1) {
            return redirect()->back()->with('error', 'User is not a farmer');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($farmer->id),
            ],
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update the farmer's details
        $farmer->name = $validated['name'];
        $farmer->email = $validated['email'];
        $farmer->phone = $validated['phone'] ?? $farmer->phone;
        $farmer->location = $validated['location'] ?? $farmer->location;

        // Update password only if provided
        if (!empty($validated['password'])) {
            $farmer->password = Hash::make($validated['password']);
        }

        $farmer->save();

        return redirect()->route('admin.farmers')->with('success', 'Farmer updated successfully');
    }

    /**
     * Remove the specified farmer from storage.
     */
    public function destroy($id)
    {
        $farmer = User::findOrFail($id);

        // Ensure we're deleting a farmer
        if ($farmer->role_id != 1) {
            return redirect()->back()->with('error', 'User is not a farmer');
        }

        $farmer->delete();

        return redirect()->route('admin.farmers')->with('success', 'Farmer deleted successfully');
    }
}
