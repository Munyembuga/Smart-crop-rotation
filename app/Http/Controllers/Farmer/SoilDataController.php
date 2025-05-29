<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\SoilData;
use App\Models\Device;
use App\Models\Farm;
use App\Models\CropHistory;
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
        $farmer = Auth::user();

        // Get farmer's farms and devices
        $farms = Farm::where('user_id', $farmer->id)->get();
        $devices = Device::where('user_id', $farmer->id)->where('status', 'active')->get();

        // Get latest soil data for each device
        $latestSoilData = SoilData::whereHas('device', function($query) use ($farmer) {
            $query->where('user_id', $farmer->id);
        })->with(['device', 'farm'])
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

        // Calculate summary statistics
        $stats = [
            'total_devices' => $devices->count(),
            'active_readings' => SoilData::whereHas('device', function($query) use ($farmer) {
                $query->where('user_id', $farmer->id);
            })->where('created_at', '>=', Carbon::now()->subHour())->count(),
            'total_farms' => $farms->count(),
            'health_score' => $this->calculateOverallHealthScore($farmer->id)
        ];

        return view('farmer.soil_management_farmer', compact('farms', 'devices', 'latestSoilData', 'stats'));
    }

    /**
     * Get live/current soil data for farmer's devices
     */
    public function liveData(Request $request)
    {
        $farmer = Auth::user();

        $query = SoilData::whereHas('device', function($q) use ($farmer) {
            $q->where('user_id', $farmer->id);
        })->with(['device', 'farm']);

        // Apply filters if provided
        if ($request->device_id) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->farm_id) {
            $query->where('farm_id', $request->farm_id);
        }

        // Get latest readings for each device
        $liveData = $query->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('device_id')
            ->map(function($readings) {
                return $readings->first();
            });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $liveData->values(),
                'updated_at' => now()->toISOString()
            ]);
        }

        return view('farmer.soil_live_data', compact('liveData'));
    }

    /**
     * Get soil recommendations for farmer's land
     */
    public function recommendations(Request $request)
    {
        $farmer = Auth::user();

        // Get recent soil data for analysis
        $recentSoilData = SoilData::whereHas('device', function($q) use ($farmer) {
            $q->where('user_id', $farmer->id);
        })->with(['device', 'farm', 'cropHistory'])
        ->where('created_at', '>=', Carbon::now()->subDays(7))
        ->orderBy('created_at', 'desc')
        ->get();

        $recommendations = [];

        foreach ($recentSoilData->groupBy('device_id') as $deviceId => $readings) {
            $latestReading = $readings->first();
            $deviceRecommendations = $this->generateRecommendations($latestReading);

            if (!empty($deviceRecommendations)) {
                $recommendations[] = [
                    'device' => $latestReading->device,
                    'farm' => $latestReading->farm,
                    'latest_reading' => $latestReading,
                    'recommendations' => $deviceRecommendations
                ];
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        }

        return view('farmer.soil_recommendations', compact('recommendations'));
    }

    /**
     * Get soil history data for farmer's devices
     */
    public function history(Request $request)
    {
        $farmer = Auth::user();

        $query = SoilData::whereHas('device', function($q) use ($farmer) {
            $q->where('user_id', $farmer->id);
        })->with(['device', 'farm']);

        // Apply date filters
        if ($request->start_date) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date));
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        // Apply device filter
        if ($request->device_id) {
            $query->where('device_id', $request->device_id);
        }

        // Apply farm filter
        if ($request->farm_id) {
            $query->where('farm_id', $request->farm_id);
        }

        $historyData = $query->orderBy('created_at', 'desc')->paginate(50);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $historyData->items(),
                'pagination' => [
                    'current_page' => $historyData->currentPage(),
                    'last_page' => $historyData->lastPage(),
                    'total' => $historyData->total()
                ]
            ]);
        }

        return view('farmer.soil_history', compact('historyData'));
    }

    /**
     * Get analytics data for farmer's soil health
     */
    public function analytics(Request $request)
    {
        $farmer = Auth::user();

        // Get data for the last 30 days
        $startDate = Carbon::now()->subDays(30);

        $analyticsData = SoilData::whereHas('device', function($q) use ($farmer) {
            $q->where('user_id', $farmer->id);
        })->where('created_at', '>=', $startDate)
        ->with(['device', 'farm'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Process data for charts
        $charts = [
            'ph_trend' => $this->processTrendData($analyticsData, 'ph'),
            'moisture_trend' => $this->processTrendData($analyticsData, 'moisture'),
            'temperature_trend' => $this->processTrendData($analyticsData, 'temperature'),
            'health_distribution' => $this->processHealthDistribution($analyticsData)
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'charts' => $charts
            ]);
        }

        return view('farmer.soil_analytics', compact('charts'));
    }

    /**
     * Manual soil data input for farmers
     */
    public function manualInput()
    {
        $farmer = Auth::user();
        $farms = Farm::where('user_id', $farmer->id)->get();
        $devices = Device::where('user_id', $farmer->id)->get();

        return view('farmer.soil_manual_input', compact('farms', 'devices'));
    }

    /**
     * Store manually entered soil data
     */
    public function storeManualData(Request $request)
    {
        $farmer = Auth::user();

        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'farm_id' => 'required|exists:farms,id',
            'ph' => 'required|numeric|between:0,14',
            'moisture' => 'required|numeric|between:0,100',
            'temperature' => 'required|numeric|between:-10,60',
            'nitrogen' => 'nullable|numeric|between:0,1000',
            'phosphorus' => 'nullable|numeric|between:0,1000',
            'potassium' => 'nullable|numeric|between:0,1000',
            'notes' => 'nullable|string|max:1000',
            'crop_type' => 'nullable|string|max:100',
            'season' => 'nullable|string|max:50'
        ]);

        // Verify ownership
        $device = Device::where('id', $validated['device_id'])
                        ->where('user_id', $farmer->id)
                        ->firstOrFail();

        $farm = Farm::where('id', $validated['farm_id'])
                   ->where('user_id', $farmer->id)
                   ->firstOrFail();

        // Calculate soil health score
        $healthScore = $this->calculateSoilHealthScore($validated);

        // Create soil data record
        $soilData = SoilData::create([
            'device_id' => $validated['device_id'],
            'farm_id' => $validated['farm_id'],
            'ph' => $validated['ph'],
            'moisture' => $validated['moisture'],
            'temperature' => $validated['temperature'],
            'nitrogen' => $validated['nitrogen'],
            'phosphorus' => $validated['phosphorus'],
            'potassium' => $validated['potassium'],
            'soil_health_score' => $healthScore,
            'notes' => $validated['notes'],
            'is_manual' => true,
            'created_by' => $farmer->id
        ]);

        // Create crop history if provided
        if ($validated['crop_type']) {
            CropHistory::create([
                'soil_data_id' => $soilData->id,
                'farm_id' => $validated['farm_id'],
                'crop_type' => $validated['crop_type'],
                'season' => $validated['season'] ?? $this->getCurrentSeason(),
                'planted_date' => now(),
                'user_id' => $farmer->id
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Soil data recorded successfully',
                'soil_data' => $soilData->load(['device', 'farm'])
            ]);
        }

        return redirect()->route('farmer.soil.analysis-results', $soilData->id)
                        ->with('success', 'Soil data recorded successfully');
    }

    /**
     * Show analysis results for a specific soil data entry
     */
    public function analysisResults($soilDataId)
    {
        $farmer = Auth::user();

        $soilData = SoilData::whereHas('device', function($q) use ($farmer) {
            $q->where('user_id', $farmer->id);
        })->with(['device', 'farm', 'cropHistory'])
        ->findOrFail($soilDataId);

        $analysis = [
            'health_status' => $this->getHealthStatus($soilData->soil_health_score),
            'recommendations' => $this->generateRecommendations($soilData),
            'comparison' => $this->getHistoricalComparison($soilData),
            'optimal_crops' => $this->suggestOptimalCrops($soilData)
        ];

        return view('farmer.soil_analysis_results', compact('soilData', 'analysis'));
    }

    /**
     * Get crop history for a specific farm
     */
    public function getCropHistory(Request $request)
    {
        $farmer = Auth::user();

        $farmId = $request->farm_id;

        // Verify farm ownership
        $farm = Farm::where('id', $farmId)
                   ->where('user_id', $farmer->id)
                   ->firstOrFail();

        $cropHistory = CropHistory::where('farm_id', $farmId)
                                 ->with(['soilData'])
                                 ->orderBy('planted_date', 'desc')
                                 ->get();

        return response()->json([
            'success' => true,
            'crop_history' => $cropHistory
        ]);
    }

    /**
     * Get filters data for dropdowns
     */
    public function getFilters()
    {
        $farmer = Auth::user();

        $devices = Device::where('user_id', $farmer->id)->get(['id', 'name', 'type']);
        $farms = Farm::where('user_id', $farmer->id)->get(['id', 'name', 'location']);

        $seasons = CropHistory::whereHas('farm', function($q) use ($farmer) {
            $q->where('user_id', $farmer->id);
        })->distinct()->pluck('season')->filter();

        return response()->json([
            'success' => true,
            'devices' => $devices,
            'farms' => $farms,
            'seasons' => $seasons
        ]);
    }

    /**
     * Generate demo data for testing
     */
    public function generateDemoData()
    {
        $farmer = Auth::user();

        $devices = Device::where('user_id', $farmer->id)->get();
        $farms = Farm::where('user_id', $farmer->id)->get();

        if ($devices->isEmpty() || $farms->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'You need at least one device and one farm to generate demo data'
            ]);
        }

        $demoEntries = 0;

        foreach ($devices as $device) {
            foreach ($farms as $farm) {
                for ($i = 0; $i < 5; $i++) {
                    $soilData = SoilData::create([
                        'device_id' => $device->id,
                        'farm_id' => $farm->id,
                        'ph' => round(rand(50, 80) / 10, 1),
                        'moisture' => rand(20, 80),
                        'temperature' => rand(15, 35),
                        'nitrogen' => rand(10, 100),
                        'phosphorus' => rand(5, 50),
                        'potassium' => rand(10, 80),
                        'soil_health_score' => rand(60, 95),
                        'is_manual' => false,
                        'created_at' => Carbon::now()->subDays(rand(0, 30))
                    ]);

                    $demoEntries++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Generated {$demoEntries} demo soil data entries"
        ]);
    }

    // Private helper methods

    private function calculateOverallHealthScore($farmerId)
    {
        $avgScore = SoilData::whereHas('device', function($q) use ($farmerId) {
            $q->where('user_id', $farmerId);
        })->where('created_at', '>=', Carbon::now()->subDays(7))
        ->avg('soil_health_score');

        return round($avgScore ?? 0, 1);
    }

    private function calculateSoilHealthScore($data)
    {
        $score = 0;

        // pH score (0-30 points)
        $ph = $data['ph'];
        if ($ph >= 6.0 && $ph <= 7.5) {
            $score += 30;
        } elseif ($ph >= 5.5 && $ph <= 8.0) {
            $score += 20;
        } else {
            $score += 10;
        }

        // Moisture score (0-25 points)
        $moisture = $data['moisture'];
        if ($moisture >= 40 && $moisture <= 70) {
            $score += 25;
        } elseif ($moisture >= 30 && $moisture <= 80) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Temperature score (0-25 points)
        $temp = $data['temperature'];
        if ($temp >= 18 && $temp <= 28) {
            $score += 25;
        } elseif ($temp >= 15 && $temp <= 35) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Nutrient score (0-20 points)
        $nitrogen = $data['nitrogen'] ?? 0;
        $phosphorus = $data['phosphorus'] ?? 0;
        $potassium = $data['potassium'] ?? 0;

        if ($nitrogen >= 20 && $phosphorus >= 10 && $potassium >= 15) {
            $score += 20;
        } elseif ($nitrogen >= 10 && $phosphorus >= 5 && $potassium >= 10) {
            $score += 10;
        }

        return min(100, $score);
    }

    private function generateRecommendations($soilData)
    {
        $recommendations = [];

        // pH recommendations
        if ($soilData->ph < 6.0) {
            $recommendations[] = [
                'type' => 'pH Management',
                'priority' => 'high',
                'message' => 'Soil is too acidic. Consider adding lime to raise pH.',
                'action' => 'Add 2-3 kg lime per square meter'
            ];
        } elseif ($soilData->ph > 7.5) {
            $recommendations[] = [
                'type' => 'pH Management',
                'priority' => 'high',
                'message' => 'Soil is too alkaline. Consider adding sulfur or organic matter.',
                'action' => 'Add compost or sulfur to lower pH'
            ];
        }

        // Moisture recommendations
        if ($soilData->moisture < 30) {
            $recommendations[] = [
                'type' => 'Irrigation',
                'priority' => 'high',
                'message' => 'Soil moisture is low. Increase irrigation frequency.',
                'action' => 'Water immediately and adjust irrigation schedule'
            ];
        } elseif ($soilData->moisture > 80) {
            $recommendations[] = [
                'type' => 'Drainage',
                'priority' => 'medium',
                'message' => 'Soil moisture is high. Improve drainage to prevent root rot.',
                'action' => 'Check drainage systems and reduce watering'
            ];
        }

        // Temperature recommendations
        if ($soilData->temperature < 15) {
            $recommendations[] = [
                'type' => 'Temperature',
                'priority' => 'medium',
                'message' => 'Soil temperature is low. Consider mulching or row covers.',
                'action' => 'Apply organic mulch to warm soil'
            ];
        } elseif ($soilData->temperature > 35) {
            $recommendations[] = [
                'type' => 'Temperature',
                'priority' => 'medium',
                'message' => 'Soil temperature is high. Provide shade or increase watering.',
                'action' => 'Use shade cloth or increase irrigation frequency'
            ];
        }

        // Nutrient recommendations
        if ($soilData->nitrogen && $soilData->nitrogen < 20) {
            $recommendations[] = [
                'type' => 'Fertilization',
                'priority' => 'medium',
                'message' => 'Nitrogen levels are low. Apply nitrogen-rich fertilizer.',
                'action' => 'Apply urea or compost'
            ];
        }

        return $recommendations;
    }

    private function getHealthStatus($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 70) return 'Good';
        if ($score >= 60) return 'Fair';
        return 'Poor';
    }

    private function processTrendData($data, $field)
    {
        return $data->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($dayData) use ($field) {
            return $dayData->avg($field);
        });
    }

    private function processHealthDistribution($data)
    {
        $distribution = [
            'excellent' => 0,
            'good' => 0,
            'fair' => 0,
            'poor' => 0
        ];

        foreach ($data as $reading) {
            $status = $this->getHealthStatus($reading->soil_health_score);
            $distribution[strtolower($status)]++;
        }

        return $distribution;
    }

    private function getHistoricalComparison($soilData)
    {
        $previousData = SoilData::where('device_id', $soilData->device_id)
                               ->where('id', '!=', $soilData->id)
                               ->where('created_at', '<', $soilData->created_at)
                               ->orderBy('created_at', 'desc')
                               ->first();

        if (!$previousData) {
            return null;
        }

        return [
            'ph_change' => round($soilData->ph - $previousData->ph, 1),
            'moisture_change' => round($soilData->moisture - $previousData->moisture, 1),
            'temperature_change' => round($soilData->temperature - $previousData->temperature, 1),
            'health_change' => round($soilData->soil_health_score - $previousData->soil_health_score, 1)
        ];
    }

    private function suggestOptimalCrops($soilData)
    {
        $crops = [];

        // Simple crop suggestions based on soil conditions
        if ($soilData->ph >= 6.0 && $soilData->ph <= 7.0 && $soilData->moisture >= 40) {
            $crops[] = ['name' => 'Tomatoes', 'suitability' => 'High'];
            $crops[] = ['name' => 'Peppers', 'suitability' => 'High'];
        }

        if ($soilData->ph >= 5.5 && $soilData->ph <= 6.5) {
            $crops[] = ['name' => 'Potatoes', 'suitability' => 'High'];
            $crops[] = ['name' => 'Carrots', 'suitability' => 'Medium'];
        }

        if ($soilData->moisture >= 60) {
            $crops[] = ['name' => 'Rice', 'suitability' => 'High'];
            $crops[] = ['name' => 'Lettuce', 'suitability' => 'Medium'];
        }

        return $crops;
    }

    private function getCurrentSeason()
    {
        $month = date('n');
        if ($month >= 3 && $month <= 5) return 'Spring';
        if ($month >= 6 && $month <= 8) return 'Summer';
        if ($month >= 9 && $month <= 11) return 'Autumn';
        return 'Winter';
    }

    /**















































































































































































































































































































































































































































































}    }        ];            'confidence' => $confidence            'priority' => $priority,            'details' => $details,            'crop' => $recommendedCrop,        return [        }            $details .= ' Improve drainage to prevent waterlogging.';        } elseif ($moisture > 80) {            $details .= ' Increase irrigation or water retention.';        if ($moisture < 30) {        }            $details .= ' Add organic matter to lower pH.';        } elseif ($ph > 7.5) {            $details .= ' Add lime to increase pH.';        if ($ph < 6.0) {        }            $confidence = 60;            $priority = 'low';            $details = 'Soil requires treatment before planting. Consider pH adjustment and moisture management.';            $recommendedCrop = 'Soil Improvement';        } else {            $confidence = 80;            $priority = 'medium';            $details = 'Good soil conditions. Consider soil amendments for optimal yield.';            $recommendedCrop = $availableCrops[1] ?? $availableCrops[0];        } elseif ($ph >= 5.5 && $ph <= 7.5 && $moisture >= 30 && $moisture <= 80) {            $confidence = 95;            $priority = 'high';            $details = 'Excellent soil conditions detected. This crop should perform very well.';            $recommendedCrop = $availableCrops[0];        if ($ph >= 6.0 && $ph <= 7.0 && $moisture >= 40 && $moisture <= 70) {        $confidence = 70;        $priority = 'medium';        $details = '';        $recommendedCrop = 'Mixed Vegetables';        $availableCrops = $isSeasonA ? $seasonACrops : $seasonBCrops;        $seasonBCrops = ['Beans', 'Potato', 'Sweet Potato', 'Soybeans', 'Cassava'];        $seasonACrops = ['Maize', 'Rice', 'Sorghum', 'Groundnuts', 'Wheat'];        $isSeasonA = ($currentMonth >= 9 || $currentMonth <= 2);        $currentMonth = Carbon::now()->month;        $temperature = $soilData->temperature;        $moisture = $soilData->moisture;        $ph = $soilData->ph;    {    private function generateRecommendation($soilData)    }        return min(100, $score);        }            $score += 10;        } elseif ($nitrogen >= 10 && $phosphorus >= 5 && $potassium >= 10) {            $score += 20;        if ($nitrogen >= 20 && $phosphorus >= 10 && $potassium >= 15) {        }            $score += 5;        } else {            $score += 15;        } elseif ($temperature >= 15 && $temperature <= 35) {            $score += 25;        if ($temperature >= 18 && $temperature <= 28) {        }            $score += 5;        } else {            $score += 15;        } elseif ($moisture >= 30 && $moisture <= 80) {            $score += 25;        if ($moisture >= 40 && $moisture <= 70) {        }            $score += 10;        } else {            $score += 20;        } elseif ($ph >= 5.5 && $ph <= 8.0) {            $score += 30;        if ($ph >= 6.0 && $ph <= 7.5) {        $score = 0;    {    private function calculateSoilHealthScore($ph, $moisture, $temperature, $nitrogen, $phosphorus, $potassium)    }        }            ], 500);                'message' => 'Error generating demo data: ' . $e->getMessage()                'success' => false,            return response()->json([        } catch (\Exception $e) {            ]);                'count' => $count                'message' => "Generated {$count} demo readings successfully!",                'success' => true,            return response()->json([            }                }                    $count++;                    ]);                        'created_at' => Carbon::now()->subHours(rand(1, 72))                        'created_by' => Auth::id(),                        'is_manual' => false,                        'notes' => 'Demo data for testing',                        'soil_health_score' => $healthScore,                        'potassium' => $potassium,                        'phosphorus' => $phosphorus,                        'nitrogen' => $nitrogen,                        'temperature' => $temperature,                        'moisture' => $moisture,                        'ph' => $ph,                        'farm_id' => $device->farm_id,                        'device_id' => $device->id,                    SoilData::create([                    $healthScore = $this->calculateSoilHealthScore($ph, $moisture, $temperature, $nitrogen, $phosphorus, $potassium);                    $potassium = rand(15, 60);                    $phosphorus = rand(5, 40);                    $nitrogen = rand(10, 80);                    $temperature = round(15 + (mt_rand() / mt_getrandmax()) * 20, 1);                    $moisture = round(20 + (mt_rand() / mt_getrandmax()) * 60, 1);                    $ph = round(5.5 + (mt_rand() / mt_getrandmax()) * 2.5, 1);                for ($i = 0; $i < $readings; $i++) {                                $readings = rand(5, 10);                // Generate 5-10 demo readings per device            foreach ($devices as $device) {            $count = 0;            }                ]);                    'message' => 'No active devices found. Please contact admin to set up devices.'                    'success' => false,                return response()->json([            if ($devices->isEmpty()) {            $devices = Device::where('user_id', Auth::id())->where('status', 'active')->get();            // Get user's devices        try {    {    public function generateDemoData(Request $request)     */     * Generate demo data for testing    /**    }        return view('farmer.soil_analysis_results', compact('soil_data', 'recommendation', 'cropHistory'));            ->get();            ->take(5)            ->orderBy('planted_date', 'desc')        $cropHistory = CropHistory::where('farm_id', $soil_data->farm_id)                $recommendation = $this->generateRecommendation($soil_data);                $soil_data->load(['device.user', 'farm', 'createdBy']);        }            abort(403, 'Access denied');        if ($soil_data->device->user_id !== Auth::id()) {        // Verify access    {    public function analysisResults(SoilData $soil_data)     */     * Show analysis results for a specific soil data entry    /**    }        }            return back()->withErrors(['error' => 'Error saving soil data: ' . $e->getMessage()]);        } catch (\Exception $e) {                ->with('success', 'Soil data recorded successfully!');            return redirect()->route('farmer.soil.analysis-results', $soilData->id)            ]);                'created_by' => Auth::id()                'is_manual' => true,                'notes' => $request->notes,                'soil_health_score' => $healthScore,                'potassium' => $request->potassium,                'phosphorus' => $request->phosphorus,                'nitrogen' => $request->nitrogen,                'temperature' => $request->temperature,                'moisture' => $request->moisture,                'ph' => $request->ph,                'farm_id' => $request->farm_id,                'device_id' => $request->device_id,            $soilData = SoilData::create([            );                $request->potassium ?? 0                $request->phosphorus ?? 0,                $request->nitrogen ?? 0,                $request->temperature,                $request->moisture,                $request->ph,            $healthScore = $this->calculateSoilHealthScore(            // Calculate soil health score (same logic as admin)        try {        }            return back()->withErrors(['device_id' => 'Device not found or access denied.']);        if (!$device) {                        ->first();                        ->where('user_id', Auth::id())        $device = Device::where('id', $request->device_id)        // Verify device belongs to current user        ]);            'notes' => 'nullable|string|max:1000'            'potassium' => 'nullable|numeric|min:0',            'phosphorus' => 'nullable|numeric|min:0',            'nitrogen' => 'nullable|numeric|min:0',            'temperature' => 'required|numeric|min:-50|max:60',            'moisture' => 'required|numeric|min:0|max:100',            'ph' => 'required|numeric|min:0|max:14',            'farm_id' => 'required|exists:farms,id',            'device_id' => 'required|exists:devices,id',        $request->validate([    {    public function storeManualData(Request $request)     */     * Store manually entered soil data    /**    }        return view('farmer.soil_manual_input', compact('farms', 'devices'));                $devices = Device::with('farm')->where('user_id', Auth::id())->where('status', 'active')->get();        $farms = Farm::where('user_id', Auth::id())->get();    {    public function manualInput()     */     * Manual soil data input for farmers    /**    }        }            ], 500);                'message' => 'Error loading filters: ' . $e->getMessage()                'success' => false,            return response()->json([        } catch (\Exception $e) {            ]);                'seasons' => $seasons                'farms' => $farms,                'devices' => $devices,                'success' => true,            return response()->json([            $seasons = ['Season A', 'Season B'];            $farms = Farm::where('user_id', Auth::id())->get();            $devices = Device::where('user_id', Auth::id())->where('status', 'active')->get();        try {    {    public function getFilters()     */     * Get filters data for dropdowns    /**    }        }            ], 500);                'message' => 'Error loading analytics: ' . $e->getMessage()                'success' => false,            return response()->json([        } catch (\Exception $e) {            ]);                'season' => $season                'health_distribution' => $healthDistribution,                'stats' => $stats,                'success' => true,            return response()->json([            $season = ($currentMonth >= 9 || $currentMonth <= 2) ? 'Season A' : 'Season B';            $currentMonth = Carbon::now()->month;            ->get();            ->groupBy('health_status')            )                DB::raw('COUNT(*) as count')                '),                    END as health_status                        ELSE "poor"                        WHEN soil_health_score >= 40 THEN "fair"                        WHEN soil_health_score >= 60 THEN "good"                        WHEN soil_health_score >= 80 THEN "excellent"                    CASE                 DB::raw('            ->select(            })                $q->where('user_id', $userId);            $healthDistribution = SoilData::whereHas('device', function($q) use ($userId) {            // Health distribution for user's data            ];                })->count()                    $q->where('user_id', $userId);                'total_readings' => SoilData::whereHas('device', function($q) use ($userId) {                'total_farms' => Farm::where('user_id', $userId)->count(),                'total_devices' => Device::where('user_id', $userId)->where('status', 'active')->count(),            $stats = [                        $userId = Auth::id();        try {    {    public function analytics(Request $request)     */     * Get analytics data for farmer's soil health    /**    }        }            ], 500);                'message' => 'Error loading history: ' . $e->getMessage()                'success' => false,            return response()->json([        } catch (\Exception $e) {            ]);                'data' => $data->items()                'success' => true,            return response()->json([            $data = $query->paginate(50);            }                $query->whereDate('created_at', '<=', $request->end_date);            if ($request->end_date) {            }                $query->whereDate('created_at', '>=', $request->start_date);            if ($request->start_date) {                ->orderBy('created_at', 'desc');                })                    $q->where('user_id', Auth::id());                ->whereHas('device', function($q) {            $query = SoilData::with(['device.user', 'farm'])        try {    {    public function history(Request $request)     */     * Get soil history data for farmer's devices    /**    }        }            ], 500);                'message' => 'Error loading recommendations: ' . $e->getMessage()                'success' => false,            return response()->json([        } catch (\Exception $e) {            ]);                'season' => $season                'data' => $recommendations,                'success' => true,            return response()->json([            $season = ($currentMonth >= 9 || $currentMonth <= 2) ? 'Season A' : 'Season B';            $currentMonth = Carbon::now()->month;            });                ];                    ] : null                        'name' => $data->farm->name                        'id' => $data->farm->id,                    'farm' => $data->farm ? [                    'created_at' => $data->created_at,                    'confidence_score' => $recommendation['confidence'],                    'priority' => $recommendation['priority'],                    'recommendation_details' => $recommendation['details'],                    'recommended_crop' => $recommendation['crop'],                    'id' => $data->id,                return [                $recommendation = $this->generateRecommendation($data);            $recommendations = $soilData->map(function($data) {                ->get();                ->take(10)                ->orderBy('created_at', 'desc')                })                    $q->where('user_id', Auth::id());                ->whereHas('device', function($q) {            $soilData = SoilData::with(['device.user', 'farm'])        try {    {    public function recommendations(Request $request)     */     * Get soil recommendations for farmer's land    /**    }        }            ], 500);                'message' => 'Error loading live data: ' . $e->getMessage()                'success' => false,            return response()->json([        } catch (\Exception $e) {            ]);                'season' => $season                }),                    ];                        ] : null                            'name' => $item->farm->name                            'id' => $item->farm->id,                        'farm' => $item->farm ? [                        ] : null,                            'device_serial_number' => $item->device->device_serial_number                            'device_name' => $item->device->device_name,                            'id' => $item->device->id,                        'device' => $item->device ? [                        'recorded_at' => $item->created_at,                        'soil_health_score' => $item->soil_health_score,                        'potassium_level' => $item->potassium,                        'phosphorus_level' => $item->phosphorus,                        'nitrogen_level' => $item->nitrogen,                        'temperature' => $item->temperature,                        'moisture_level' => $item->moisture,                        'ph_level' => $item->ph,                        'id' => $item->id,                    return [                'data' => $data->map(function($item) {                'success' => true,            return response()->json([            $season = ($currentMonth >= 9 || $currentMonth <= 2) ? 'Season A' : 'Season B';            $currentMonth = Carbon::now()->month;            $data = $query->take(50)->get();            $query->where('created_at', '>=', Carbon::now()->subDay());            // Get recent data (last 24 hours)            }                $query->where('device_id', $request->device_id);            if ($request->device_id) {                ->orderBy('created_at', 'desc');                })                    $q->where('user_id', Auth::id());                ->whereHas('device', function($q) {            $query = SoilData::with(['device.user', 'farm'])            // Get only current user's data        try {
    {    public function liveData(Request $request)     */     * Get live/current soil data for farmer's devices
