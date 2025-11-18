<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    

    public function register(Request $request)  
    {
        sleep(1);

        $fields = $request->validate([
            'avatar' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if($request->hasFile('avatar')){
            $fields['avatar'] = Storage::disk('public')->put('avatars', $request->file('avatar'));
        }

        $user = User::create($fields);

        Auth::login($user);

        return redirect()->route('dashboard')->with('greet', 'Welcome to the platform, ' . $user->name . '!');

    }

    public function login(Request $request)
    {
        sleep(1);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))->with('greet', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
