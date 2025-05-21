<?php

use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::get('/login', function() {
    return view('login');
})->name('login');

Route::post('/login', function(Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Update last login time
        $user = Auth::user();
        $user->last_login = now();
        $user->save();

        // Flash success message
        $request->session()->flash('success', 'Login successful! Welcome back, ' . $user->name);

        // Redirect based on role
        if ($user->role_id == 4) { // System Admin
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('farmer.dashboard');
        }
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.process');

// Registration Routes
Route::get('/register', function() {
    return view('register');
})->name('register');

Route::post('/register', function(Request $request) {
    $validated = $request->validate([
        'username' => ['required', 'string', 'max:255', 'unique:users'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'phone' => ['nullable', 'string', 'max:20'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // Get farmer role (ID 1)
    $farmerRoleId = 1;

    $user = User::create([
        'name' => $validated['username'],
        'username' => $validated['username'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'password' => Hash::make($validated['password']),
        'role_id' => $farmerRoleId,
        'status' => 'active',
    ]);

    Auth::login($user);

    return redirect()->route('farmer.dashboard');
})->name('register.process');

Route::post('/logout', function(Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Farmer dashboard route
    Route::get('/farmer/dashboard', function () {
        if (Auth::user()->role_id != 1) {
            return redirect()->route('admin.dashboard');
        }
        return view('farmer.dashboard');
    })->name('farmer.dashboard');

    // Admin dashboard route
    Route::get('/admin/dashboard', function () {
        if (Auth::user()->role_id != 4) {
            return redirect()->route('farmer.dashboard');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Routes for farm management
    Route::post('/addfarm', [FarmController::class, 'addFarm']);

    Route::get('/addfarm', function () {
        return view('addfarm');
    })->name('addfarm');

    Route::get('/farm', function () {
        $farms = Farm::all();
        return view('farm', ['farms' => $farms]);
    })->name('farm');

    // Routes for Device management
    Route::get('/userdevice', function () {
        return view('userdevice');
    })->name('userdevice');

    Route::get('/backtohome', function () {
        return view('home');
    })->name('backtohome');
});

// Admin routes - using class directly instead of alias
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Farmer Management
    Route::get('/farmers', function () {
        return view('admin.farmer_management');
    })->name('farmers');
    // user management
Route::get('/users', function () {
        return view('admin.user_management');
    })->name('users');
    // Device Management
    Route::get('/devices', function () {
        return view('admin.device_management');
    })->name('devices');

    // Reports
    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('reports');

    // Crop Database
    Route::get('/crops', function () {
        return view('admin.crops');
    })->name('crops');

    // System Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    // Role management
    Route::resource('roles', RoleController::class);

    // User management
    Route::resource('users', AdminUserController::class);

    // Farmer management routes
    Route::get('/farmers', [App\Http\Controllers\Admin\FarmerController::class, 'index'])->name('farmers');
    Route::post('/farmers', [App\Http\Controllers\Admin\FarmerController::class, 'store'])->name('farmers.store');
    Route::get('/farmers/{id}', [App\Http\Controllers\Admin\FarmerController::class, 'show'])->name('farmers.show');
    Route::put('/farmers/{id}', [App\Http\Controllers\Admin\FarmerController::class, 'update'])->name('farmers.update');
    Route::delete('/farmers/{id}', [App\Http\Controllers\Admin\FarmerController::class, 'destroy'])->name('farmers.destroy');
});
