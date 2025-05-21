<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Smart Crop Rotation System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background-color: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #2d3748;
        }

        p {
            font-size: 1.1rem;
            color: #4a5568;
            margin-bottom: 2rem;
        }

        a.button {
            padding: 0.75rem 1.5rem;
            background-color: #2b6cb0;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        a.button:hover {
            background-color: #2c5282;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Smart Crop Rotation System</h1>
        <p>Efficiently manage your crop rotation strategies for better yield and sustainability.</p>
        <a href="{{ route('login') }}" class="button">Login</a>
    </div>
</body>
</html>
