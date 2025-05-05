<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Smart Crop Rotation System</title>
    {{-- Link your external CSS --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="container">
        <h1 class="welcome">Welcome to Smart Crop Rotation System</h1>

        <div class="login-form">
            <h2>Login</h2>

            <form action="/login" method="POST">
                @csrf

                <label for="email">Email Or Phone <span class="required">*</span></label>
                <input type="text" id="email" name="email" required>

                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" required>

                <div class="forgot-password">
                    <a href="">Forgot Password?</a>
                </div>

                <button type="submit" class="login-button">LOGIN</button>

                <p class="register-link">
                    Don’t Have an Account? 
                    <a href="{{ route('register') }}">Create New Account</a>
                </p>
            </form>
        </div>
    </div>

</body>
</html>
