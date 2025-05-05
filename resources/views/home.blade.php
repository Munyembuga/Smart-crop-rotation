<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body style="margin:0; padding:0; font-family:Arial, sans-serif; background:#f5f5f5;">
  
  @auth
  <!-- Navigation Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: #fff; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); position: relative; z-index: 2;">
  <div style="display: flex; align-items: center;">
    <div style="display: flex; flex-direction: column; align-items: flex-start;">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px;" />
      <span style="font-weight: bold; margin-top: 5px;">Crop Rotation System</span>
    </div>
  </div>
  <div style="display: flex; align-items: center; gap: 15px;">
    <form action="/logout" method="POST">
      @csrf
    <button style="padding: 5px 15px; background-color: white; border: 1px solid #000; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#00cc66'" onmouseout="this.style.backgroundColor='white'">Logout</button>
    
    </form>
    <button style="padding: 5px 15px; background-color: #00cc66; color: white; border: none; border-radius: 5px; cursor: pointer;">Sign Up</button>
  </div>
</div>

<!-- Hero Section -->
<div style="position: relative; background-image: url('{{ asset('images/maize_crop.png') }}'); background-size: cover; background-position: center; color: white; padding: 100px 20px; text-align: center;">
  <h1>Welcome to Smart Crop Rotation System</h1>
  <h2 style="margin-top: 20px;">Preserve Your Soil Quality Through Smart Crop Rotation!</h2>
  <button onclick="window.location.href='{{ route('farm') }}'" style="margin-top: 20px; padding: 10px 25px; background-color: #00cc66; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Get Started</button>
</div>

<!-- Stats Section -->
<div style="display: flex; justify-content: center; flex-wrap: wrap; background-color: #f9f9f9; padding: 40px 20px;">
  <div style="background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 10px; width: 150px; text-align: center;">
    <h3>Total Users</h3>
    <p>58</p>
  </div>
  <div style="background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 10px; width: 150px; text-align: center;">
    <h3>Total Plots</h3>
    <p>128</p>
  </div>
  <div style="background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 10px; width: 150px; text-align: center;">
    <h3>Active Devices</h3>
    <p>283</p>
  </div>
  <div style="background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 10px; width: 150px; text-align: center; color: red;">
    <h3>Inactive Devices</h3>
    <p>8</p>
  </div>
</div>

{{-- <div style="padding: 40px 20px; text-align: center;">
  <h2>All Farms</h2>
  <div style="display: flex; justify-content: center; flex-wrap: wrap; margin-top: 20px;">

    @foreach ($farms as $farm)
        
    <div style="margin: 10px; width: 200px;">
      <p> {{$farm['plot_number']}} </p>
      <p> {{$farm['location']}} </p>
    </div>

    @endforeach
  </div>
</div> --}}

<!-- Journey Section -->
<div style="padding: 40px 20px; text-align: center;">
  <h2>Start Your Journey With Us And Enjoy Productive Soil!</h2>
  <div style="display: flex; justify-content: center; flex-wrap: wrap; margin-top: 20px;">
    <div style="margin: 10px; width: 200px;"><p>1. The Soil Quality is Preserved</p></div>
    <div style="margin: 10px; width: 200px;"><p>2. The Soil Harvest is increased</p></div>
    <div style="margin: 10px; width: 200px;"><p>3. Rotate With Confidence</p></div>
    <div style="margin: 10px; width: 200px;"><p>4. Enjoy More Profit</p></div>
  </div>
</div>

<!-- How It Works Section -->
<div style="background-color: #f9f9f9; padding: 40px 20px; text-align: center;">
  <h2>How It Works</h2>
  <div style="display: flex; justify-content: center; flex-wrap: wrap; margin-top: 20px;">
    <div style="margin: 20px; width: 200px;"><p>Buy Your Device and apply it accordingly</p></div>
    <div style="margin: 20px; width: 200px;"><p>Sign Up On The Platform</p></div>
    <div style="margin: 20px; width: 200px;"><p>Get Seasonal Crop Recommendations</p></div>
    <div style="margin: 20px; width: 200px;"><p>Practice and Enjoy Productivity</p></div>
  </div>
</div>

<!-- Partners Section -->
<div style="padding: 40px 20px; text-align: center;">
  <h2>Our Partners</h2>
  <div style="display: flex; justify-content: center; flex-wrap: wrap; margin-top: 20px; align-items: center;">
    <img src="{{ asset('images/minagri.png') }}" alt="Partner 1" style="height: 60px; margin: 30px;" />
    <img src="{{ asset('images/rab.png') }}" alt="Partner 2" style="height: 60px; margin: 30px;" />
    <img src="{{ asset('images/naeb.png') }}" alt="Partner 3" style="height: 60px; margin: 30px;" />
    <img src="{{ asset('images/rica.png') }}" alt="Partner 4" style="height: 60px; margin: 30px;" />
    <img src="{{ asset('images/rca.png') }}" alt="Partner 5" style="height: 60px; margin: 30px;" />
  </div>
</div>

<!-- Footer -->
<div style="background-color: #333; color: white; padding: 40px 20px; display: flex; flex-wrap: wrap; justify-content: space-around;">
  <div style="margin: 20px;">
    <h3>Contact Us</h3>
    <p>Phone: +250 786 736 328</p>
    <p>Email: info@scr.com</p>
    <p>Address: KN 7, Kigali Rwanda</p>
  </div>
  <div style="margin: 20px;">
    <h3>Quick Links</h3>
    <p><a href="https://rwasis.rab.gov.rw/" target="_blank" style="color: white; transition: transfrom 0.2s ease; text-decoration: none;" >Rwanda Soil Information System</a></p>
    <p>Smart Nkunganire</p>
    <p>Weather Forecast</p>
  </div>
  <div style="margin: 20px;">
    <h3>Support</h3>
    <p>Device Monitoring</p>
    <p>Plot Management</p>
    <p>Request Support</p>
  </div>
  <div style="margin: 20px;">
    <img src="[MAP_IMAGE_LINK]" alt="Map" style="width: 200px; height: auto;" />
  </div>
</div>



  @else

   <!-- Header -->
  <div style="background:white; padding:20px; text-align:center; border-bottom:2px solid #ddd;">
    <h2 style="margin:0;">Welcome to Smart Crop Rotation System</h2>
  </div>

  <!-- Centered Form Container -->
  <div style="display:flex; justify-content:center; align-items:center; height:85vh;">
    <div style="background:white; padding:30px 40px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); width:350px;">
      
      <!-- Title -->
      <h2 style="text-align:center; margin-bottom:30px;">Login</h2>

      <form action="/login" method="POST">
        @csrf
      
      <!-- Email/Phone Field -->
      <label for="email" style="font-weight:bold;">Email <span style="color:red;">*</span></label>
      <input type="text" id="email" name="email" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;">

      <!-- Password Field -->
      <label for="password" style="font-weight:bold;">Password <span style="color:red;">*</span></label>
      <input type="password" id="password" name="password" style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ccc; border-radius:5px;">

      <!-- Forgot Password (optional) -->
      <div style="text-align:right; margin-bottom:20px;">
        <a href="#" style="font-size:13px; color:blue; text-decoration:none;">Forgot Password?</a>
      </div>

      <!-- Register Button -->
      <button style="width:100%; background:#00c851; color:white; padding:12px; border:none; border-radius:8px; font-size:16px; font-weight:bold; cursor:pointer;">
        LOGIN
      </button>
     </form>
      <!-- Login Redirect -->
      <p style="text-align:center; margin-top:20px;">
        Don't Have an Account? <a href="{{ route('register') }}" style="color:blue; text-decoration:none; font-weight:bold;">Create New Account</a>
      </p>
    </div>
  </div>   


  @endauth

  

</body>
</html>