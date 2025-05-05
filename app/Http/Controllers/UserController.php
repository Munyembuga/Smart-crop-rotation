<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function login(Request $request){
        $incomingFields = $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        
        if(auth()->attempt(['email'=>$incomingFields['email'], 'password'=>$incomingFields['password']])){
            $request->session()->regenerate();
            return redirect('/');
        }

        return redirect('/login');

    }
    public function logout(){
        auth()->logout();
        return redirect('/');
    }
    public function register(Request $request){
        $incomingFields = $request -> validate([
            'name'=> ['required', 'min:3', 'max:25', Rule::unique('users','name')],
            'email'=> ['required', 'email', Rule::unique('users', 'email')],
            'password'=> ['required', 'min:8', 'max:200']
        ]);
        
        $incomingFields['password']= bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);
        auth()->login($user);

        return redirect('/');
    }
}
