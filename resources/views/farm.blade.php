<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Management</title>
    <link rel="stylesheet" href="{{ asset('css/farm.css') }}">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <a href="{{ route('backtohome')}}">← Back To Home</a>
                <h1>Crop Rotation System</h1>
            </div>
            <ul class="nav">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="icon dashboard-icon"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="icon overview-icon"></i> Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="icon statistics-icon"></i> Statistics
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('userdevice')}}" class="nav-link">
                        <i class="icon device-icon"></i> Device Management
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="#" class="nav-link">
                        <i class="icon farm-icon"></i> Farm Management
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="icon settings-icon"></i> Settings
                    </a>
                </li>
            </ul>
            <button class="logout-btn">Logout</button>
        </aside>
        <main class="content">
            <header class="header">
                <div class="welcome">Welcome Back, Osuald</div>
                <div class="user-actions">
                    <button class="notification-btn">
                        <i class="icon notification-bell"></i>
                    </button>
                    <div class="user-profile">
                        <i class="icon user-avatar"></i>
                    </div>
                </div>
            </header>
            <div class="farm-management-section">
                <div class="section-header">
                    <h2>Farm Management</h2>
                    <div class="actions">
                        <div class="view-by">
                            View By:
                            <select>
                                <option>All</option>
                                <option>Location</option>
                            </select>
                        </div>
                        <button onclick="window.location.href='{{ route('addfarm') }}'" class="add-farm-btn">+ Add Farm</button>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                {{-- <th>Farm Id</th> --}}
                                <th>Plot Number</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($farms as $farm)
                            <tr>
                                {{-- <td>1</td> --}}
                                <td>{{$farm['plot_number']}}</td>
                                <td>{{$farm['description']}}</td>
                                <td>{{$farm['location']}}</td>
                                <td class="actions-cell">
                                    <button class="delete-btn">Delete</button>
                                    <button class="edit-btn">Edit</button>
                                    <button class="details-btn">Details</button>
                                </td>
                            </tr>
                            @endforeach                           
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <button class="prev-btn">Prev</button>
                    <div class="page-numbers">
                        <button class="page-btn">1</button>
                        <button class="page-btn active">2</button>
                        <button class="page-btn">...</button>
                        <button class="page-btn">Next</button>
                    </div>
                    <button class="next-btn">Next</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>