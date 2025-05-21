<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([





























































}    }        return redirect('/');        $request->session()->regenerateToken();        $request->session()->invalidate();        Auth::logout();    {    public function logout(Request $request)     */     * @return \Illuminate\Http\RedirectResponse     * @param  \Illuminate\Http\Request  $request     *     * Log the user out of the application.    /**    }        }            return redirect()->route('farmer.dashboard');        } else {            return redirect()->route('admin.dashboard');        if ($user->role_id == 4) { // System Admin        // Redirect based on role        $user->save();        $user->last_login = now();        // Update last login time        $request->session()->flash('success', 'Login successful! Welcome back, ' . $user->name);        // Flash success message    {    protected function authenticated(Request $request, $user)     */     * @return mixed     * @param  mixed  $user     * @param  \Illuminate\Http\Request  $request     *     * The user has been authenticated.    /**    }        ])->onlyInput('email');            'email' => 'The provided credentials do not match our records.',        return back()->withErrors([        }            return redirect()->intended('dashboard');            $user->save();            $user->last_login = now();            $user = Auth::user();            // Update last login time            $request->session()->regenerate();        if (Auth::attempt($credentials)) {        ]);
            'password' => ['required'],            'email' => ['required', 'email'],
