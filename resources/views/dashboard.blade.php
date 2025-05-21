<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - Smart Crop Rotation</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4caf50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-info span {
            margin-right: 15px;
        }
        .content {
            display: flex;
            margin-top: 20px;
        }
        .sidebar {
            width: 250px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .main {
            flex: 1;
            margin-left: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .menu-item {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .menu-item:hover {
            background-color: #f0f0f0;
        }
        .menu-item.active {
            background-color: #e8f5e9;
            color: #4caf50;
            font-weight: bold;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #f9f9f9;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }
        .stat-card h3 {
            margin-top: 0;
            color: #666;
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #4caf50;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 8px 16px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .logout-form {
            display: inline;
        }
        .logout-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Smart Crop Rotation System</h1>
        <div class="user-info">
            <span>Welcome, {{ Auth::user()->name }}</span>
            <form class="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="content">
            <div class="sidebar">
                <div class="menu-item active">Dashboard</div>
                <div class="menu-item">My Farms</div>
                <div class="menu-item">Crop History</div>
                <div class="menu-item">Rotation Plans</div>
                <div class="menu-item">Soil Data</div>
                <div class="menu-item">Reports</div>
                <div class="menu-item">Settings</div>
            </div>

            <div class="main">
                <h2>Farmer Dashboard</h2>

                <div class="stats">
                    <div class="stat-card">
                        <h3>Total Farms</h3>
                        <div class="number">0</div>
                    </div>
                    <div class="stat-card">
                        <h3>Active Plots</h3>
                        <div class="number">0</div>
                    </div>
                    <div class="stat-card">
                        <h3>Current Crops</h3>
                        <div class="number">0</div>
                    </div>
                    <div class="stat-card">
                        <h3>Recommendations</h3>
                        <div class="number">0</div>
                    </div>
                </div>

                <div class="actions">
                    <a href="{{ route('addfarm') }}" class="btn">Add New Farm</a>
                    <a href="{{ route('farm') }}" class="btn">View Farms</a>
                </div>

                <div class="recent-activity">
                    <h3>Recent Activity</h3>
                    <p>No recent activity to display.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
