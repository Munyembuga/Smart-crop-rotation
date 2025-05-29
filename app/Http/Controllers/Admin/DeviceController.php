<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    /**
     * Display a listing of devices.
     */
    public function index()
    {
        try {
            $devices = Device::with(['user', 'assignedBy', 'farm'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get all active users (farmers) for the dropdown
            $users = User::where('status', 'active')
                        ->where('role_id', 1) // Only farmers
                        ->orderBy('name')
                        ->get();

            // Get all farms for the dropdown
            $farms = Farm::with('user')->orderBy('name')->get();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'devices' => $devices,
                    'users' => $users,
                    'farms' => $farms
                ]);
            }

            return view('admin.device_management', compact('devices', 'users', 'farms'));
        } catch (\Exception $e) {
            \Log::error('Device index error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load devices: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to load devices: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created device.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_serial_number' => 'required|string|max:255|unique:devices,device_serial_number',
                'device_name' => 'required|string|max:255',
                'device_type' => 'required|string|max:255',
                'user_id' => 'required|exists:users,id',
                'farm_id' => 'nullable|exists:farms,id',
                'installation_location' => 'required|string|max:255',
                'farm_upi' => 'nullable|string|max:255',
                'sensor_types' => 'nullable|array',
                'sensor_types.*' => 'string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'notes' => 'nullable|string|max:1000',
                'firmware_version' => 'nullable|string|max:50',
                'status' => 'required|in:active,inactive,maintenance,offline',
                'battery_level' => 'nullable|integer|between:0,100'
            ]);

            // Verify that the farm belongs to the user if farm_id is provided
            if ($validated['farm_id']) {
                $farm = Farm::where('id', $validated['farm_id'])
                           ->where('user_id', $validated['user_id'])
                           ->first();

                if (!$farm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected farm does not belong to the selected user',
                        'errors' => ['farm_id' => ['The selected farm does not belong to the selected user.']]
                    ], 422);
                }
            }

            $validated['assigned_by'] = auth()->id();
            $validated['installed_at'] = now();

            $device = Device::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Device created successfully',
                    'device' => $device->load(['user', 'assignedBy', 'farm'])
                ]);
            }

            return redirect()->route('admin.devices')->with('success', 'Device added successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            \Log::error('Device store error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating device: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error creating device: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified device.
     */
    public function show($id)
    {
        try {
            $device = Device::with(['user', 'assignedBy', 'farm', 'soilData' => function($query) {
                $query->latest()->take(10);
            }])->findOrFail($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'device' => $device
                ]);
            }

            return view('admin.device_show', compact('device'));

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'Device not found');
        }
    }

    /**
     * Update the specified device.
     */
    public function update(Request $request, $id)
    {
        try {
            $device = Device::findOrFail($id);

            $rules = [
                'device_serial_number' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('devices', 'device_serial_number')->ignore($device->id),
                ],
                'device_name' => 'required|string|max:255',
                'device_type' => 'required|string|max:255',
                'user_id' => 'required|exists:users,id',
                'farm_id' => 'nullable|exists:farms,id',
                'installation_location' => 'required|string|max:255',
                'farm_upi' => 'nullable|string|max:255',
                'sensor_types' => 'nullable|array',
                'sensor_types.*' => 'string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'notes' => 'nullable|string|max:1000',
                'firmware_version' => 'nullable|string|max:50',
                'status' => 'required|in:active,inactive,maintenance,offline',
                'battery_level' => 'nullable|integer|between:0,100'
            ];

            $validated = $request->validate($rules);

            // Verify that the farm belongs to the user if farm_id is provided
            if ($validated['farm_id']) {
                $farm = Farm::where('id', $validated['farm_id'])
                           ->where('user_id', $validated['user_id'])
                           ->first();

                if (!$farm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected farm does not belong to the selected user',
                        'errors' => ['farm_id' => ['The selected farm does not belong to the selected user.']]
                    ], 422);
                }
            }

            $device->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Device updated successfully',
                    'device' => $device->load(['user', 'assignedBy', 'farm'])
                ]);
            }

            return redirect()->route('admin.devices')->with('success', 'Device updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            \Log::error('Device update error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating device: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error updating device: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified device.
     */
    public function destroy($id)
    {
        try {
            $device = Device::findOrFail($id);

            // Check if device has associated soil data
            $soilDataCount = $device->soilData()->count();

            if ($soilDataCount > 0) {
                $message = "Device has {$soilDataCount} soil data records. Deleting device will also delete all associated data.";
            }

            $device->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Device deleted successfully'
                ]);
            }

            return redirect()->route('admin.devices')->with('success', 'Device deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Device delete error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting device: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error deleting device: ' . $e->getMessage());
        }
    }

    /**
     * Update device status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $device = Device::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:active,inactive,maintenance,offline'
            ]);

            $device->update($validated);

            // Update last_maintenance_at if status is maintenance
            if ($validated['status'] === 'maintenance') {
                $device->update(['last_maintenance_at' => now()]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Device status updated successfully',
                    'device' => $device->load(['user', 'assignedBy', 'farm'])
                ]);
            }

            return redirect()->back()->with('success', 'Device status updated successfully');

        } catch (\Exception $e) {
            \Log::error('Device status update error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating device status: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error updating device status: ' . $e->getMessage());
        }
    }

    /**
     * Get farms for a specific user
     */
    public function getFarmsForUser(Request $request)
    {
        try {
            $userId = $request->get('user_id');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $farms = Farm::where('user_id', $userId)->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'farms' => $farms
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching farms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update device status
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_ids' => 'required|array',
                'device_ids.*' => 'exists:devices,id',
                'status' => 'required|in:active,inactive,maintenance,offline'
            ]);

            $updatedCount = Device::whereIn('id', $validated['device_ids'])
                                 ->update(['status' => $validated['status']]);

            // Update maintenance timestamp if needed
            if ($validated['status'] === 'maintenance') {
                Device::whereIn('id', $validated['device_ids'])
                      ->update(['last_maintenance_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated status for {$updatedCount} devices"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating device statuses: ' . $e->getMessage()
            ], 500);
        }
    }
}
