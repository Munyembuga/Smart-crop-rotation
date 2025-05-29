<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\SoilData;
use App\Models\Device;
use App\Models\Farm;
use App\Models\SoilRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SoilDataController extends Controller
{
    /**
     * Display the main soil management dashboard for farmers
     */
    public function index()
    {
        return view('farmer.soil_management_farmer');
    }

    /**
     * Get live/current soil data for farmer's devices
     */
    public function liveData(Request $request)
    {
        try {
            $userId = Auth::id();

            $query = SoilData::with(['device.user', 'farm'])
                ->whereHas('device', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->orderBy('recorded_at', 'desc');

            // Apply filters
            if ($request->device_id) {
                $query->where('device_id', $request->device_id);
            }

            if ($request->farm_id) {
                $query->where('farm_id', $request->farm_id);
            }

            $data = $query->limit(50)->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'season' => $this->getCurrentSeason()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading live data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get soil recommendations for farmer's land
     */
    public function recommendations(Request $request)
    {
        try {
            $userId = Auth::id();

            $data = SoilRecommendation::with(['user', 'farm'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'season' => $this->getCurrentSeason()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading recommendations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get soil history data for farmer's devices
     */
    public function history(Request $request)
    {
        try {
            $userId = Auth::id();

            $query = SoilData::with(['device.user', 'farm'])
                ->whereHas('device', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->orderBy('created_at', 'desc');

            // Apply date filters
            if ($request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->season) {
                $query->where('season', $request->season);
            }

            $data = $query->paginate(50);

            return response()->json([
                'success' => true,
                'data' => $data->items()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics data for farmer's soil health
     */
    public function analytics(Request $request)
    {
        try {
            $userId = Auth::id();

            // Get stats for farmer's devices only
            $totalDevices = Device::where('user_id', $userId)->count();
            $totalFarms = Farm::where('user_id', $userId)->count();
            $totalReadings = SoilData::whereHas('device', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count();

            $avgHealthScore = SoilData::whereHas('device', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->avg('soil_health_score') ?? 0;

            // Health distribution for farmer's data
            $healthDistribution = SoilData::whereHas('device', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->select(
                DB::raw('
                    CASE
                        WHEN soil_health_score >= 80 THEN "excellent"
                        WHEN soil_health_score >= 60 THEN "good"
                        WHEN soil_health_score >= 40 THEN "fair"
                        ELSE "poor"
                    END as health_status
                '),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('health_status')
            ->get();

            // Trends for last 30 days
            $trends = SoilData::whereHas('device', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('recorded_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(recorded_at) as date'),
                DB::raw('AVG(ph_level) as avg_ph'),
                DB::raw('AVG(moisture_level) as avg_moisture'),
                DB::raw('AVG(temperature) as avg_temperature')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_devices' => $totalDevices,
                    'total_farms' => $totalFarms,
                    'total_readings' => $totalReadings,
                    'avg_health_score' => round($avgHealthScore, 1)
                ],
                'health_distribution' => $healthDistribution,
                'trends' => $trends,
                'season' => $this->getCurrentSeason()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filters data for dropdowns
     */
    public function getFilters(Request $request)
    {
        try {
            $userId = Auth::id();

            $devices = Device::where('user_id', $userId)
                ->with('user')
                ->get(['id', 'device_name', 'device_serial_number']);

            $farms = Farm::where('user_id', $userId)
                ->get(['id', 'name', 'location']);

            $seasons = ['Season A', 'Season B', 'Season C']; // You can make this dynamic

            return response()->json([
                'success' => true,
                'devices' => $devices,
                'farms' => $farms,
                'seasons' => $seasons
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading filters: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate demo data for testing
     */
    public function generateDemoData(Request $request)
    {
        try {
            $userId = Auth::id();

            // Check if user has any devices
            $devices = Device::where('user_id', $userId)->get();

            if ($devices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No devices found for your account. Please contact admin to set up devices first.'
                ]);
            }

            $count = 0;
            foreach ($devices as $device) {
                // Generate 5-10 sample readings per device
                $readings = rand(5, 10);

                for ($i = 0; $i < $readings; $i++) {
                    SoilData::create([
                        'device_id' => $device->id,
                        'farm_id' => $device->farm_id,
                        'ph_level' => round(rand(55, 85) / 10, 1), // 5.5 to 8.5
                        'moisture_level' => rand(20, 80),
                        'temperature' => rand(18, 35),
                        'nitrogen' => rand(10, 50),
                        'phosphorus' => rand(5, 25),
                        'potassium' => rand(15, 40),
                        'soil_health_score' => rand(40, 95),
                        'recorded_at' => Carbon::now()->subHours(rand(1, 168)), // Last week
                        'season' => 'Season A'
                    ]);
                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Generated {$count} demo soil readings for your devices successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating demo data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual soil data input for farmers
     */
    public function manualInput()
    {
        // Return manual input form view for farmers
        return view('farmer.soil_manual_input');
    }

    /**
     * Store manually entered soil data
     */
    public function storeManualData(Request $request)
    {
        // Handle manual soil data input for farmers
        // Implementation similar to admin but filtered by farmer's devices
    }

    /**
     * Show analysis results for a specific soil data entry
     */
    public function analysisResults($soilDataId)
    {
        // Show analysis results for specific soil data
        // Implementation for farmers
    }

    /**
     * Get crop history for a specific farm
     */
    public function getCropHistory(Request $request)
    {
        // Get crop history for farmer's farms
        // Implementation for farmers
    }

    private function getCurrentSeason()
    {
        $month = date('n');
        if ($month >= 3 && $month <= 5) {
            return 'Spring Season';
        } elseif ($month >= 6 && $month <= 8) {
            return 'Summer Season';
        } elseif ($month >= 9 && $month <= 11) {
            return 'Fall Season';
        } else {
            return 'Winter Season';
        }
    }
}
