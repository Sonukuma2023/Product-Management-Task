<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index(){
         
        return Inertia::render('Auth/RegisterComponent');
    }
   public function post(Request $request) {
    // 1. Validate incoming data
    $data = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'phone'    => 'required|string|min:10', // Changed 'size:10' to 'min:10' to be safer with special characters
        'address'  => 'nullable|string',
    ]);

    // 2. Create the user record
    User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => \Hash::make($data['password']),
        'phone'    => $data['phone'] ?? null,
        'address'  => $data['address'] ?? null,
        'Role'     => 'users',
    ]);

    // 3. Use Inertia friendly redirection
    return redirect('/login')->with('message', 'Registration successful!');
}


    public function login(){
        return Inertia::render('Auth/LoginComponent');
    }
    
    public function loginStore(Request $request)
    {
        $credentials = $request->validate([
            'email'   => 'required|email',
            'password'=> 'required',
        ]);

        if (Auth::attempt($credentials)) {
            
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on user role
            if ($user->role === 'users') {
                return redirect()->intended(route('user.dashboard'));
            }

            // if ($user->role === 'administrator') {
            //     return redirect()->intended(route('dashboard'));
            // }
            // return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials are incorrect.',
        ])->onlyInput('email');
    }


}
