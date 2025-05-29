@extends('layouts.farmer')

@section('title', 'My Soil Management')

@section('styles')
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
        background: linear-gradient(135deg, #2c5530 0%, #4a7c4f 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
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
        border-color: #2c5530;
        transform: translateX(5px);
    }

    .soil-option i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
        color: #2c5530;
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
        background: linear-gradient(135deg, #2c5530 0%, #4a7c4f 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 600;
    }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #2c5530;
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
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #2c5530 0%, #4a7c4f 100%);">
                    <h3 class="mb-0"><i class="fas fa-seedling me-2"></i>My Soil Management Dashboard</h3>
                    <div>
                        <span class="season-badge me-2" id="currentSeason">Loading...</span>
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
                        <h4 class="mb-0" id="totalDevices">-</h4>
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
                        <h4 class="mb-0" id="totalFarms">-</h4>
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
                        <p class="mb-1 text-muted">My Readings</p>
                        <h4 class="mb-0" id="totalReadings">-</h4>
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
                        <h4 class="mb-0" id="healthScore">-</h4>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-heartbeat fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div id="mainContent">
        <!-- Live Data Section -->
        <div id="liveDataSection" class="content-section active">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-satellite-dish me-2"></i>My Live Soil Data</h5>
                </div>
                <div class="card-body">
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-select" id="deviceFilter">
                                    <option value="">All My Devices</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="farmFilter">
                                    <option value="">All My Farms</option>
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
                            <p class="mt-2">Loading my soil data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations Section -->
        <div id="recommendationsSection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>My Soil Recommendations</h5>
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
                                <input type="date" class="form-control" id="startDate">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Season</label>
                                <select class="form-select" id="seasonFilter">
                                    <option value="">All Seasons</option>
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
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentSection = 'liveData';
let filters = {};

// Add CSRF token function
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        return token.getAttribute('content');
    }
    return '';
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadInitialData();
    loadFilters();
    showSection('liveData');
});

function loadInitialData() {
    fetch('/farmer/soil/analytics', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('totalDevices').textContent = data.stats.total_devices || 0;
            document.getElementById('totalFarms').textContent = data.stats.total_farms || 0;
            document.getElementById('totalReadings').textContent = data.stats.total_readings || 0;
            document.getElementById('healthScore').textContent = Math.round(data.stats.avg_health_score || 0) + '%';
            document.getElementById('currentSeason').textContent = data.season || 'Current Season';
        }
    })
    .catch(error => {
        console.error('Error loading stats:', error);
        document.getElementById('totalDevices').textContent = '0';
        document.getElementById('totalFarms').textContent = '0';
        document.getElementById('totalReadings').textContent = '0';
        document.getElementById('healthScore').textContent = '0%';
        document.getElementById('currentSeason').textContent = 'Current Season';
    });
}

function loadFilters() {
    fetch('/farmer/soil/filters', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            filters = data;
            populateFilters();
        }
    })
    .catch(error => {
        console.error('Error loading filters:', error);
        filters = { devices: [], farms: [], seasons: ['Season A', 'Season B'] };
        populateFilters();
    });
}

function populateFilters() {
    const deviceSelect = document.getElementById('deviceFilter');
    if (deviceSelect) {
        deviceSelect.innerHTML = '<option value="">All My Devices</option>';
        if (filters.devices && filters.devices.length > 0) {
            filters.devices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.id;
                option.textContent = `${device.device_name || device.name}`;
                deviceSelect.appendChild(option);
            });
        }
    }

    const farmSelect = document.getElementById('farmFilter');
    if (farmSelect) {
        farmSelect.innerHTML = '<option value="">All My Farms</option>';
        if (filters.farms && filters.farms.length > 0) {
            filters.farms.forEach(farm => {
                const option = document.createElement('option');
                option.value = farm.id;
                option.textContent = `${farm.name} - ${farm.location || 'Location'}`;
                farmSelect.appendChild(option);
            });
        }
    }
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
    hideSoilModal();

    document.querySelectorAll('.content-section').forEach(el => {
        el.classList.remove('active');
    });

    const sectionElement = document.getElementById(section + 'Section');
    if (sectionElement) {
        sectionElement.classList.add('active');
        currentSection = section;

        switch(section) {
            case 'liveData':
                loadLiveData();
                break;
        }
    }
}

function loadLiveData() {
    const deviceId = document.getElementById('deviceFilter')?.value || '';
    const farmId = document.getElementById('farmFilter')?.value || '';

    const params = new URLSearchParams();
    if (deviceId) params.append('device_id', deviceId);
    if (farmId) params.append('farm_id', farmId);

    document.getElementById('liveDataContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Fetching my soil data...</p>
        </div>
    `;

    fetch(`/farmer/soil/live-data?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderLiveData(data.data, data.season);
        } else {
            throw new Error(data.message || 'Failed to load live data');
        }
    })
    .catch(error => {
        console.error('Error loading live data:', error);
        document.getElementById('liveDataContent').innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>No data available yet:</strong> ${error.message}
                <br><small>You don't have any devices set up or soil data recorded yet.</small>
                <button onclick="generateDemoData()" class="btn btn-sm btn-primary ms-2">Generate Demo Data</button>
            </div>
        `;
    });
}

function renderLiveData(data, season) {
    let html = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6>Current Season: <span class="season-badge">${season || 'Unknown'}</span></h6>
            <small class="text-muted">${data ? data.length : 0} readings from my devices</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>My Device</th>
                        <th>My Farm</th>
                        <th>pH Level</th>
                        <th>Moisture</th>
                        <th>Temperature</th>
                        <th>Health Score</th>
                        <th>Recorded</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (!data || data.length === 0) {
        html += `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    No live data available from your devices.
                    <button onclick="generateDemoData()" class="btn btn-sm btn-outline-primary ms-1">Generate Demo Data</button>
                    to create sample readings.
                </td>
            </tr>
        `;
    } else {
        data.forEach(item => {
            const health = getHealthStatus(item.soil_health_score);
            html += `
                <tr>
                    <td>
                        <div class="fw-bold">${item.device ? item.device.device_name : 'Unknown Device'}</div>
                        <small class="text-muted">${item.device ? item.device.device_serial_number : 'N/A'}</small>
                    </td>
                    <td>${item.farm ? item.farm.name : 'N/A'}</td>
                    <td><span class="badge bg-info">${item.ph_level || 'N/A'}</span></td>
                    <td><span class="badge bg-primary">${item.moisture_level ? item.moisture_level + '%' : 'N/A'}</span></td>
                    <td><span class="badge bg-warning">${item.temperature ? item.temperature + '°C' : 'N/A'}</span></td>
                    <td>
                        <span class="badge bg-${getHealthColor(health)}">${health}</span>
                        <small class="d-block text-muted">${item.soil_health_score || 0}%</small>
                    </td>
                    <td><small>${new Date(item.recorded_at).toLocaleString()}</small></td>
                </tr>
            `;
        });
    }

    html += '</tbody></table></div>';
    document.getElementById('liveDataContent').innerHTML = html;
}

function getHealthStatus(score) {
    if (!score && score !== 0) return 'unknown';
    score = parseFloat(score);
    if (score >= 80) return 'excellent';
    if (score >= 60) return 'good';
    if (score >= 40) return 'fair';
    return 'poor';
}

function getHealthColor(health) {
    switch(health) {
        case 'excellent': return 'success';
        case 'good': return 'info';
        case 'fair': return 'warning';
        case 'poor': return 'danger';
        default: return 'secondary';
    }
}

function generateDemoData() {
    Swal.fire({
        title: 'Generate Demo Data?',
        text: 'This will create sample soil readings for your devices for testing purposes.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate data',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Generating...',
                text: 'Creating demo soil data for your farms...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });

            fetch('/farmer/soil/generate-demo-data', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Demo data generated successfully for your farms!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        loadLiveData();
                        loadInitialData();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to generate demo data', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to generate demo data: ' + error.message, 'error');
            });
        }
    });
}
</script>
@endsection
