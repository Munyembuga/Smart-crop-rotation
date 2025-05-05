<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Device Management</title>
    <link rel="stylesheet" href="{{ asset('css/userdevice.css') }}">
</head>
<body>
    <div class="sidebar">
        <a href="{{ route('backtohome')}}" class="back">← Back To Home</a>
        <h2>Crop Rotation System</h2>
        <ul>
            <li class="active"><a href="#">Dashboard</a></li>
            <li><a href="#">Overview</a></li>
            <li><a href="#">Device Management</a></li>
            <li><a href="{{ route('farm')}}">Farm Management</a></li>
            <li><a href="#">Settings</a></li>
        </ul>
        <button class="logout">Logout</button>
    </div>

    <div class="main-content">
        <header>
            <h1>Welcome back, Osuald</h1>
            <div class="profile">
                <i class="notification">🔔</i>
                <img src="https://via.placeholder.com/40" alt="Profile" />
            </div>
        </header>

        <div class="device-management">
            <div class="top-bar">
                <h3>Device Management</h3>
                <div class="actions">
                        <div class="view-by">
                            View By:
                            <select>
                                <option>All</option>
                                <option>Plot Number</option>
                                <option>Location</option>
                                <option>Status</option>
                            </select>
                            <button  class="add-btn">+ Add Device</button>
                        </div>
                        
                    </div>
            </div>

            <table>
                <thead>
                    <tr class="theader">
                        <th>Device SN</th>
                        <th>Device Name</th>
                        <th>Plot Number</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Hardcoded rows -->
                    <tr>
                        <td>A12A</td>
                        <td>IoT1</td>
                        <td>SN 12341</td>
                        <td>Nyabihu</td>
                        <td class="active">Active</td>
                        <td>
                            <button class="delete">Delete</button>
                            <button class="edit">Edit</button>
                            <button class="view">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td>A12B</td>
                        <td>IoT2</td>
                        <td>SN 12342</td>
                        <td>Nyabihu</td>
                        <td class="inactive">Inactive</td>
                        <td>
                            <button class="delete">Delete</button>
                            <button class="edit">Edit</button>
                            <button class="view">View</button>
                        </td>
                    </tr>
                    <!-- More rows... -->
                </tbody>
            </table>

            <div class="pagination">
                <button class="prev">Prev</button>
                <span class="page-number">1</span>
                <button class="next">Next</button>
            </div>
        </div>
    </div>
</body>
</html>
