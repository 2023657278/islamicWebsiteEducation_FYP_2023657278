<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdministratorController extends Controller
{
    /**
     * Display a listing of Admins.
     */
    public function index(Request $request)
    {
        // 1. Fetch only users with 'admin' role
        $query = User::where('role', 'admin');

        // 2. Search Functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        $admins = $query->latest()->get();

        return view('admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new Admin.
     */
    public function create()
    {
        return view('admins.create');
    }

    /**
     * Store a newly created Admin in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => bcrypt($request->password),
            'role' => 'admin', // Force role to admin
        ]);

        return redirect()->route('admins.index')
                         ->with('success', 'New Administrator registered successfully.');
    }

    /**
     * Show the form for editing the specified Admin.
     */
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        
        // Security: Prevent editing non-admins via this controller
        if($admin->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('admins.edit', compact('admin'));
    }

    /**
     * Update the specified Admin in storage.
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'phone_number']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $admin->update($data);

        return redirect()->route('admins.index')
                         ->with('success', 'Administrator profile updated.');
    }

    /**
     * Remove the specified Admin from storage.
     */
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        
        // Safety: Prevent deleting yourself
        if (auth()->id() == $admin->id) {
            return back()->with('error', 'You cannot delete your own account while logged in.');
        }

        $admin->delete();

        return redirect()->route('admins.index')
                         ->with('success', 'Administrator removed successfully.');
    }
}