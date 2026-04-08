<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20', // Ensure your DB column is 'phone_number'
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 1. Update Basic Fields (No Address)
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone; 

        $imageUploaded = false;
        
        // 2. Handle Image Upload (Fixed for Public Disk)
        if ($request->hasFile('profile_image')) {
            // Delete old image if it exists
            if ($user->profile_image && Storage::disk('public')->exists('profile_images/' . $user->profile_image)) {
                Storage::disk('public')->delete('profile_images/' . $user->profile_image);
            }
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $request->profile_image->getClientOriginalExtension();
            
            // Store the file specifically on the 'public' disk
            // This saves to: storage/app/public/profile_images/filename.jpg
            $request->profile_image->storeAs('profile_images', $filename, 'public');
            
            // Update user record with just the filename
            $user->profile_image = $filename;
            $imageUploaded = true;
        }

        // 3. Handle Password Change
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            } else {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect'])
                    ->withInput();
            }
        }

        $user->save();

        if ($imageUploaded) {
            return redirect()->route('profile.edit')
                ->with('success', 'Profile updated successfully!')
                ->with('has_image', true);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }    

    public function deleteImage(Request $request)
    {
        $user = Auth::user();
        
        // Check 'public' disk for the image
        if ($user->profile_image && Storage::disk('public')->exists('profile_images/' . $user->profile_image)) {
            Storage::disk('public')->delete('profile_images/' . $user->profile_image);
            $user->profile_image = null;
            $user->save();
            
            return redirect()->route('profile.edit')
                ->with('success', 'Profile image deleted successfully!');
        }
        
        return redirect()->route('profile.edit')
            ->with('error', 'No profile image to delete.');
    }
}