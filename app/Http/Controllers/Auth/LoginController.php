<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override the login method to handle dual login (Email or No. Maktab)
     */
    public function login(Request $request)
    {
        // 1. Validate the input - the field is named 'email' in your blade, but can be either
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Determine if the input is an email or a No. Maktab
        // We use filter_var to check if the text looks like an email address
        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'no_maktab';

        // 3. Create the credentials array dynamically
        $credentials = [
            $loginField => $request->email,
            'password' => $request->password,
        ];

        // 4. Attempt to log the user in
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Success! Send them to the dashboard based on their role
            return $this->sendLoginResponse($request);
        }

        // 5. If it fails, send them back with an error
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Where to redirect users after login based on their role.
     */
    protected function redirectTo()
    {
        $user = Auth::user(); 

        // 1. If Super Admin -> Go to Real Admin Panel
    if ($user->role === 'admin') {
        return route('adminreal.dashboard'); 
    }

    // 2. If Teacher -> Go to Teacher Management Panel
    if ($user->role === 'teacher') {
        return route('admin.dashboard'); 
    } 
    
    // 3. If Student -> Go to Student Homepage
    if ($user->role === 'student') {
        return route('student.homepage'); 
    }
        
        // 3. Fallback
        return '/laravel'; 
    }

    /**
     * Logout logic to prevent 419 Page Expired errors.
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    protected function authenticated(Request $request, $user)
{
    $user->update([
        'last_login_at' => now()
    ]);
}

public function redirectToGoogle()
{
    return Socialite::driver('google')->redirect();
}

}