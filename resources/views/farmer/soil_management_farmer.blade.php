@extends('layouts.farmer')

@section('title', 'Soil Management - Farmer Dashboard')

@section('styles')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Custom styles -->
<style>
    .soil-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        display: none;
    }

    .soil-dropdown-modal {
        position: fixed;
        bottom: 20px;
        left: 20px;
        width: 350px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        display: none;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .soil-modal-header {
        background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .soil-modal-body {
        padding: 20px;
    }

    .soil-option {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .soil-option:hover {
        background-color: #f8f9fa;
        border-color: #8B4513;
        transform: translateX(5px);
    }

    .soil-option i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
        color: #8B4513;
    }

    .soil-option-content h6 {
        margin: 0 0 4px 0;
        color: #333;
        font-weight: 600;
    }

    .soil-option-content small {
        color: #666;
        margin: 0;
    }

    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    .season-badge {
        background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 600;
    }

    .health-excellent { color: #28a745; }
    .health-good { color: #17a2b8; }
    .health-fair { color: #ffc107; }
    .health-poor { color: #dc3545; }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #8B4513;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);">
                    <h3 class="mb-0"><i class="fas fa-seedling me-2"></i>My Soil Management Dashboard</h3>
                    <div>
                        <span class="season-badge me-2" id="currentSeason">{{ date('Y') }} - {{ date('n') <= 6 ? 'Season A' : 'Season B' }}</span>
                        <button type="button" class="btn btn-light btn-sm" onclick="showSoilModal()">
                            <i class="fas fa-chart-line me-2"></i>View Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4" id="quickStats">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 text-muted">My Devices</p>
                        <h4 class="mb-0" id="totalDevices">{{ $stats['total_devices'] }}</h4>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-microchip fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 text-muted">My Farms</p>
                        <h4 class="mb-0" id="totalFarms">{{ $stats['total_farms'] }}</h4>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 text-muted">Recent Readings</p>
                        <h4 class="mb-0" id="activeReadings">{{ $stats['active_readings'] }}</h4>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-chart-bar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 text-muted">Health Score</p>
                        <h4 class="mb-0 health-{{ $stats['health_score'] >= 80 ? 'excellent' : ($stats['health_score'] >= 70 ? 'good' : ($stats['health_score'] >= 60 ? 'fair' : 'poor')) }}" id="healthScore">{{ $stats['health_score'] }}%</h4>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-heartbeat fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Readings Quick View -->
    @if($latestSoilData->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Latest Soil Readings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($latestSoilData->take(4) as $reading)
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-{{ $reading->health_color }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-muted">{{ $reading->device->name }}</h6>
                                            <p class="mb-1"><strong>Farm:</strong> {{ $reading->farm->name ?? 'N/A' }}</p>
                                            <p class="mb-1"><strong>pH:</strong> {{ $reading->ph }}</p>
                                            <p class="mb-1"><strong>Moisture:</strong> {{ $reading->moisture }}%</p>
                                            <p class="mb-0"><strong>Temp:</strong> {{ $reading->temperature }}°C</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="badge bg-{{ $reading->health_color }} mb-2">{{ $reading->health_status }}</span>
                                            <br>
                                            <small class="text-muted">{{ $reading->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Content Sections -->
    <div id="mainContent">
        <!-- Live Data Section -->
        <div id="liveDataSection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-satellite-dish me-2"></i>Live Soil Data</h5>
                </div>
                <div class="card-body">
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-select" id="deviceFilter">
                                    <option value="">All My Devices</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="farmFilter">
                                    <option value="">All My Farms</option>
                                    @foreach($farms as $farm)
                                        <option value="{{ $farm->id }}">{{ $farm->name }} - {{ $farm->location }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary" onclick="loadLiveData()">
                                    <i class="fas fa-refresh me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="liveDataContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading live data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations Section -->
        <div id="recommendationsSection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Soil Recommendations for My Land</h5>
                </div>
                <div class="card-body">
                    <div id="recommendationsContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-info" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading recommendations...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div id="historySection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>My Soil History</h5>
                </div>
                <div class="card-body">
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Device</label>
                                <select class="form-select" id="historyDeviceFilter">
                                    <option value="">All Devices</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-primary d-block" onclick="loadHistory()">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="historyContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading history...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Section -->
        <div id="analyticsSection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>My Soil Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="healthChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="trendsChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Soil Modal Overlay -->
<div class="soil-modal-overlay" id="soilModalOverlay" onclick="hideSoilModal()"></div>

<!-- Soil Dropdown Modal -->
<div class="soil-dropdown-modal" id="soilDropdownModal">
    <div class="soil-modal-header">
        <h6 class="mb-0"><i class="fas fa-seedling me-2"></i>My Soil Data Options</h6>
        <button type="button" class="btn-close btn-close-white" onclick="hideSoilModal()"></button>
    </div>
    <div class="soil-modal-body">
        <div class="soil-option" onclick="showSection('liveData')">
            <i class="fas fa-satellite-dish"></i>
            <div class="soil-option-content">
                <h6>Live/Current Data</h6>
                <small>Real-time soil conditions from my devices</small>
            </div>
        </div>

        <div class="soil-option" onclick="showSection('recommendations')">
            <i class="fas fa-lightbulb"></i>
            <div class="soil-option-content">
                <h6>Recommendations</h6>
                <small>Personalized crop and treatment suggestions</small>
            </div>
        </div>

        <div class="soil-option" onclick="showSection('history')">
            <i class="fas fa-history"></i>
            <div class="soil-option-content">
                <h6>My Soil History</h6>
                <small>Historical data and trends from my farms</small>
            </div>
        </div>

        <div class="soil-option" onclick="showSection('analytics')">
            <i class="fas fa-chart-pie"></i>
            <div class="soil-option-content">
                <h6>Analytics & Insights</h6>
                <small>Advanced analytics for my soil data</small>
            </div>
        </div>

        <div class="soil-option" onclick="window.location.href='{{ route('farmer.soil.manual-input') }}'">
            <i class="fas fa-edit"></i>
            <div class="soil-option-content">
                <h6>Manual Data Input</h6>
                <small>Record soil data manually for my farms</small>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentSection = 'liveData';

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadLiveData();

    // Add CSRF token to meta tag if it doesn't exist
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
});

function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

function showSoilModal() {
    document.getElementById('soilModalOverlay').style.display = 'block';
    document.getElementById('soilDropdownModal').style.display = 'block';
}

function hideSoilModal() {
    document.getElementById('soilModalOverlay').style.display = 'none';
    document.getElementById('soilDropdownModal').style.display = 'none';
}

function showSection(section) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));

    // Show selected section
    document.getElementById(section + 'Section').classList.add('active');
    currentSection = section;

    // Hide modal
    hideSoilModal();

    // Load data for the section
    switch(section) {
        case 'liveData':
            loadLiveData();
            break;
        case 'recommendations':
            loadRecommendations();
            break;
        case 'history':
            loadHistory();
            break;
        case 'analytics':
            loadAnalytics();
            break;
    }
}

function loadLiveData() {
    const deviceId = document.getElementById('deviceFilter')?.value || '';
    const farmId = document.getElementById('farmFilter')?.value || '';

    const params = new URLSearchParams();
    if (deviceId) params.append('device_id', deviceId);
    if (farmId) params.append('farm_id', farmId);

    fetch(`{{ route('farmer.soil.live') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderLiveData(data.data);
        } else {
            throw new Error(data.message || 'Failed to load live data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('liveDataContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading live data: ${error.message}
            </div>
        `;
    });
}

function renderLiveData(data) {
    const container = document.getElementById('liveDataContent');

    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No live data available</h5>
                <p class="text-muted">No recent soil readings found for your devices.</p>
                <a href="{{ route('farmer.soil.manual-input') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Manual Reading
                </a>
            </div>
        `;
        return;
    }

    let html = '<div class="row">';

    data.forEach(reading => {
        html += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-left-${reading.health_color}">
                    <div class="card-header bg-${reading.health_color} text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-microchip me-1"></i>
                            ${reading.device ? reading.device.name : 'Unknown Device'}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-primary">${reading.ph}</h5>
                                <small class="text-muted">pH Level</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-success">${reading.moisture}%</h5>
                                <small class="text-muted">Moisture</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-warning">${reading.temperature}°C</h5>
                                <small class="text-muted">Temperature</small>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-${reading.health_color}">${reading.health_status}</span>
                            <small class="text-muted">${new Date(reading.created_at).toLocaleString()}</small>
                        </div>
                        <div class="mt-2">
                            <strong>Farm:</strong> ${reading.farm ? reading.farm.name : 'N/A'}<br>
                            <strong>Health Score:</strong> ${reading.soil_health_score}%
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}

function loadRecommendations() {
    document.getElementById('recommendationsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading recommendations...</p>
        </div>
    `;

    fetch('{{ route('farmer.soil.recommendations') }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderRecommendations(data.recommendations);
        } else {
            throw new Error(data.message || 'Failed to load recommendations');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('recommendationsContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading recommendations: ${error.message}
            </div>
        `;
    });
}

function renderRecommendations(recommendations) {
    const container = document.getElementById('recommendationsContent');

    if (!recommendations || recommendations.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No recommendations available</h5>
                <p class="text-muted">Add soil data to get personalized recommendations.</p>
            </div>
        `;
        return;
    }

    let html = '';

    recommendations.forEach(item => {
        html += `
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        ${item.farm ? item.farm.name : 'Unknown Farm'} - ${item.device ? item.device.name : 'Unknown Device'}
                    </h6>
                </div>
                <div class="card-body">
        `;

        if (item.recommendations && item.recommendations.length > 0) {
            item.recommendations.forEach(rec => {
                const priorityColor = rec.priority === 'high' ? 'danger' : (rec.priority === 'medium' ? 'warning' : 'info');
                html += `
                    <div class="alert alert-${priorityColor} mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="alert-heading">${rec.type}</h6>
                                <p class="mb-1">${rec.message}</p>
                                <strong>Action:</strong> ${rec.action}
                            </div>
                            <span class="badge bg-${priorityColor}">${rec.priority.toUpperCase()}</span>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="alert alert-success">
                    <h6 class="alert-heading">Great!</h6>
                    <p class="mb-0">Your soil conditions are optimal. No immediate action required.</p>
                </div>
            `;
        }

        html += `
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function loadHistory() {
    const startDate = document.getElementById('startDate')?.value || '';
    const endDate = document.getElementById('endDate')?.value || '';
    const deviceId = document.getElementById('historyDeviceFilter')?.value || '';

    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (deviceId) params.append('device_id', deviceId);

    document.getElementById('historyContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading history...</p>
        </div>
    `;

    fetch(`{{ route('farmer.soil.history') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderHistory(data.data);
        } else {
            throw new Error(data.message || 'Failed to load history');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('historyContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading history: ${error.message}
            </div>
        `;
    });
}

function renderHistory(data) {
    const container = document.getElementById('historyContent');

    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No history found</h5>
                <p class="text-muted">No soil data found for the selected criteria.</p>
            </div>
        `;
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date/Time</th>
                        <th>Device</th>
                        <th>Farm</th>
                        <th>pH</th>
                        <th>Moisture</th>
                        <th>Temperature</th>
                        <th>Health Score</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.forEach(reading => {
        html += `
            <tr>
                <td>${new Date(reading.created_at).toLocaleString()}</td>
                <td>${reading.device ? reading.device.name : 'N/A'}</td>
                <td>${reading.farm ? reading.farm.name : 'N/A'}</td>
                <td>${reading.ph}</td>
                <td>${reading.moisture}%</td>
                <td>${reading.temperature}°C</td>
                <td>${reading.soil_health_score}%</td>
                <td><span class="badge bg-${reading.health_color}">${reading.health_status}</span></td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
}

function loadAnalytics() {
    fetch('{{ route('farmer.soil.analytics') }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderAnalytics(data.charts);
        } else {
            throw new Error(data.message || 'Failed to load analytics');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function renderAnalytics(charts) {
    // Implement chart rendering using Chart.js
    // This would require the charts data to be processed and rendered
    console.log('Analytics data:', charts);
}

// Auto-refresh live data every 5 minutes
setInterval(() => {
    if (currentSection === 'liveData') {
        loadLiveData();
    }
}, 300000);
</script>
@endsection
