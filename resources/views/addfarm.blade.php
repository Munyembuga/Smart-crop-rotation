<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Farm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/addfarm.css') }}">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo"><a href="{{ route('backtohome') }}">← Back To Home</a></div>
            <div class="navigation">
                <ul>
                    <li><a href="#"><i class="icon"></i> Dashboard</a></li>
                    <li><a href="#"><i class="icon"></i> Overview</a></li>
                    <li><a href="#"><i class="icon"></i> Statistics</a></li>
                    <li><a href="#"><i class="icon"></i> Device Management</a></li>
                    <li class="active"><a href="#"><i class="icon"></i> Farm Management</a></li>
                    <li><a href="#"><i class="icon"></i> Settings</a></li>
                </ul>
            </div>
            <div class="logout">
                <button class="logout-button">Logout</button>
            </div>
        </div>
        <div class="main-content">
            <header>
                <div class="welcome">Welcome Back, Osuald</div>
                <div class="user-actions">
                    <button class="notification-button"><i class="icon"></i></button>
                    <div class="user-profile">
                        <div class="user-avatar"><i class="icon"></i></div>
                    </div>
                </div>
            </header>
            <div class="content-area">
                <div class="form-card">
                    <div class="form-header">
                        <i class="icon add-icon"></i> Add Farm
                    </div>
                 <form action="/addfarm" method="POST">
                    @csrf
                    <div class="form-body">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" id="description" name="description" placeholder="First plot for me. And my favorite">
                        </div>
                        <div class="form-group">
                            <label for="plot_number">Plot Number</label>
                            <input type="text" id="plot_number" name="plot_number" placeholder="SN 1223420">
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" placeholder="Nyagatare">
                        </div>
                        <button type="submit" class="add-button">ADD</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>