<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Smart Crop Rotation</title>
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
        .content {
            padding: 40px 0;
            text-align: center;
        }
        .actions {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Smart Crop Rotation</h1>
        <div>
            @if(Auth::check())
                <a href="{{ route('dashboard') }}" style="color: white; text-decoration: none; margin-right: 15px;">Dashboard</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" style="color: white; text-decoration: none; margin-right: 15px;">Login</a>
                <a href="{{ route('register') }}" style="color: white; text-decoration: none;">Register</a>
            @endif
        </div>
    </div>

    <div class="container">
        <div class="content">
            <h2>Welcome to Smart Crop Rotation System</h2>
            <p>Optimize your farm productivity with data-driven crop rotation strategies.</p>

            <div class="actions">
                @if(Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn">Login</a>
                    <a href="{{ route('register') }}" class="btn">Register</a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
