<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Crop Rotation  System</title>
    <!-- External CSS -->
    <link href="{{ asset('css/farmer-dashboard.css') }}" rel="stylesheet">
    <!-- Google Fonts - Nunito -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="header">
        <h1>Smart Crop Rotation System</h1>
       <div>Welcome, {{ Auth::user()->name }}</div>

        <div class="user-info">
            <div class="notification-icon">
                <i class="fa fa-bell"></i>
            </div>
            <div class="user-menu">
                <div class="avatar">
                    <i class="fa fa-user"></i>
                </div>
                <div class="user-dropdown">
                    <span class="dropdown-item">{{ Auth::user()->name }}</span>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item" style="width:100%; text-align:left; border:none; background:none;">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Display success message if any -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
                <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        @endif

        <div class="content">
            <!-- Include sidebar component -->
            @include('components.sidebar')

            <div class="main">
                <div class="overview-card">
                    <div class="section-title">
                        <i class="fa fa-chart-line"></i> Your Soil Status
                    </div>

                    <div class="soil-stats">
                        <div class="soil-stat-card">
                            <h3>PH</h3>
                            <div class="value ph-value">5.6</div>
                        </div>

                        <div class="soil-stat-card">
                            <h3>Moisture</h3>
                            <div class="value moisture-value">21</div>
                        </div>

                        <div class="soil-stat-card">
                            <h3>Temperature</h3>
                            <div class="value temperature-value">74</div>
                        </div>

                        <div class="soil-stat-card">
                            <h3>Current Crop</h3>
                            <div class="value crop-value">Rice</div>
                        </div>
                    </div>

                    <div class="chart-container">
                        <!-- First pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 120px;"></div>
                            <div class="chart-green-bar" style="height: 90px;"></div>
                        </div>

                        <!-- Second pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 100px;"></div>
                            <div class="chart-green-bar" style="height: 110px;"></div>
                        </div>

                        <!-- Third pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 130px;"></div>
                            <div class="chart-green-bar" style="height: 50px;"></div>
                        </div>

                        <!-- Fourth pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 150px;"></div>
                            <div class="chart-green-bar" style="height: 105px;"></div>
                        </div>

                        <!-- Fifth pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 110px;"></div>
                            <div class="chart-green-bar" style="height: 120px;"></div>
                        </div>

                        <!-- Sixth pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 100px;"></div>
                            <div class="chart-green-bar" style="height: 130px;"></div>
                        </div>

                        <!-- Seventh pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 120px;"></div>
                            <div class="chart-green-bar" style="height: 140px;"></div>
                        </div>

                        <!-- Eighth pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 90px;"></div>
                            <div class="chart-green-bar" style="height: 70px;"></div>
                        </div>

                        <!-- Ninth pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 80px;"></div>
                            <div class="chart-green-bar" style="height: 110px;"></div>
                        </div>

                        <!-- Tenth pair of bars -->
                        <div class="chart-bar">
                            <div class="chart-blue-bar" style="height: 130px;"></div>
                            <div class="chart-green-bar" style="height: 100px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
