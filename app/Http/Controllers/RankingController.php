<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        // We just fetch Users because users ARE the warriors
        $rankings = User::where('role', 'student')
            ->orderBy('pvp_points', 'desc')
            ->get();

        return view('users.ranking', compact('rankings'));
    }
}
