<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Farms - Smart Crop Rotation</title>
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
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .farm-list {
            margin-top: 20px;
        }
        .farm-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .farm-card h3 {
            margin-top: 0;
        }
        .no-farms {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .farm-actions {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Smart Crop Rotation</h1>
        <div>
            <a href="{{ route('dashboard') }}" style="color: white; text-decoration: none; margin-right: 15px;">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="content">
            <h2>My Farms</h2>
            <div>
                <a href="{{ route('addfarm') }}" class="btn">Add New Farm</a>
                <a href="{{ route('dashboard') }}" class="btn">Back to Dashboard</a>
            </div>

            <div class="farm-list">
                @if(count($farms) > 0)
                    @foreach($farms as $farm)
                        <div class="farm-card">
                            <h3>{{ $farm->name }}</h3>
                            <p>Location: {{ $farm->location }}</p>
                            <p>Size: {{ $farm->size }} hectares</p>
                            <div class="farm-actions">
                                <a href="#" class="btn">View Details</a>
                                <a href="#" class="btn">Edit</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-farms">
                        <p>You don't have any farms yet. Click "Add New Farm" to create your first farm.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
