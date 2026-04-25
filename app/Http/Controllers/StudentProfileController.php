<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\QuizAttempt;

class StudentProfileController extends Controller
{
    public function show()
{
    $user = Auth::user();

    // 1. Calculate Achievement Data (Keep your existing logic)
    $attempts = QuizAttempt::where('user_id', $user->id)->get();
    
    $achievements = (object) [
        'highest_score' => $attempts->max('score') ?? 0,
        'total_quizzes' => $attempts->count(),
        'perfect_scores' => $attempts->where('score', 100)->count(),
        'telegram_status' => $user->telegram_chat_id ? 1 : 0
    ];

    // 🟢 TELEGRAM HANDSHAKE FIX: Save code to DATABASE, not just session
    // If the user isn't connected and doesn't have a code yet, generate and save one
    if (!$user->telegram_chat_id && !$user->verification_code) {
        $user->update([
            'verification_code' => strtoupper(Str::random(6))
        ]);
    }

    // Use the code from the database so the Python bot can find it
    $telegramCode = $user->verification_code;

    return view('users.profile.show', compact('user', 'telegramCode', 'achievements'));
}

    public function edit()
    {
        $user = Auth::user();
        return view('users.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return redirect()->route('student.profile.show')->with('success', 'Profile updated successfully!');
    }

    public function deleteImage()
    {
        $user = Auth::user();
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
            $user->profile_image = null;
            $user->save();
            return back()->with('success', 'Profile picture removed.');
        }
        return back()->with('error', 'No image to delete.');
    }
}