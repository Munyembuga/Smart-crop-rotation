<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Crop Rotation</title>
</head>
<body>
    <!-- Header -->
  <div style="background:white; padding:20px; text-align:center; border-bottom:2px solid #ddd;">
    <h2 style="margin:0;">Welcome to Smart Crop Rotation System</h2>
  </div>

  <!-- Centered Form Container -->
  <div style="display:flex; justify-content:center; align-items:center; height:85vh;">
    <div style="background:white; padding:30px 40px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); width:350px;">

      <!-- Title -->
      <h2 style="text-align:center; margin-bottom:30px;">Register Register</h2>

      @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      <form action="{{ route('register.process') }}" method="POST">
        @csrf
        <!-- Name Field -->
        <label for="username" style="font-weight:bold;">Username <span style="color:red;">*</span></label>
        <input type="text" id="username" name="username" value="{{ old('username') }}" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;" required>

        <!-- Email Field -->
        <label for="email" style="font-weight:bold;">Email <span style="color:red;">*</span></label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;" required>

        <!-- Phone Field -->
        <label for="phone" style="font-weight:bold;">Phone Number</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;">

        <!-- Password Field -->
        <label for="password" style="font-weight:bold;">Password <span style="color:red;">*</span></label>
        <input type="password" id="password" name="password" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;" required>

        <!-- Confirm Password Field -->
        <label for="password_confirmation" style="font-weight:bold;">Confirm Password <span style="color:red;">*</span></label>
        <input type="password" id="password_confirmation" name="password_confirmation" style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #ccc; border-radius:5px;" required>

        <!-- Register Button -->
        <button type="submit" style="width:100%; background:#00c851; color:white; padding:12px; border:none; border-radius:8px; font-size:16px; font-weight:bold; cursor:pointer;">
          REGISTER
        </button>
      </form>

      <!-- Login Redirect -->
      <p style="text-align:center; margin-top:20px;">
        Already Have an Account? <a href="{{ route('login') }}" style="color:blue; text-decoration:none; font-weight:bold;">Login</a>
      </p>
    </div>
  </div>
</body>
</html>
