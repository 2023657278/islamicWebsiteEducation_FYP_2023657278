<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Where to redirect users after login.
     */
    protected function redirectTo()
    {
        $user = Auth::user(); 

        // 1. If Teacher -> Go to Admin Panel
        if ($user->role === 'teacher' || $user->role === 'admin') {
            return route('admin.dashboard'); // Returns '/adminhome'
        } 
        
        // 2. If Student -> Go to Student Homepage
        elseif ($user->role === 'student') {
            return route('student.homepage'); // Returns '/homepage'
        } 
        
        // 3. Fallback
        return '/laravel'; 
    }

    /**
     * Fix for 419 Page Expired (Logout)
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect back to Login page
        return redirect('/login');
    }
}