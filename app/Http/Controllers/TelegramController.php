<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TelegramController extends Controller
{
    /**
     * 1. The Connection View
     * This displays the page where students see the bot instructions.
     * Matches Route: telegram.connect
     */
    public function connect()
    {
        // 🟢 FIX: Fetch the authenticated student user instance
        $user = Auth::user();

        // 🟢 FIX: Generate or fetch a unique sync code parameter for the student
        // If your database doesn't have a 'telegram_code' column, we can use their 'no_maktab' 
        // or uniquely hash their ID so the Python bot can identify them instantly.
        $code = $user->no_maktab ?? ('PAI-' . ($user->id + 1000));

        // Pass both variables cleanly to the view layout template parameters
        return view('users.telegram.connect', compact('user', 'code')); 
    }
    /**
     * 2. The Handshake (Verify)
     * This checks if the Python bot has updated the chat_id in the database.
     * Matches Route: telegram.verify
     */
    public function verify(Request $request)
    {
        // Get the currently logged-in student
        $user = Auth::user();

        // Refresh data from the database to check for updates from the bot
        $user->refresh();

        if ($user->telegram_chat_id) {
            return redirect()->route('student.profile.show')->with('success', 'Telegram connected successfully!');
        }

        return redirect()->back()->with('error', 'We haven\'t received your code on Telegram yet. Please search for @PAI_MRSM_bot and send your code.');
    }

    /**
     * 3. The Disconnect (Unlink)
     * This removes the telegram_chat_id to disconnect the bot.
     * Matches Route: telegram.unlink
     */
    public function unlink(Request $request)
    {
        $user = Auth::user();

        // Set the ID to null to break the connection
        $user->update([
            'telegram_chat_id' => null
        ]);

        return redirect()->route('student.profile.show')->with('success', 'Telegram disconnected successfully!');
    }

    /**
     * Optional: Webhook endpoint
     * Not required if you are using polling with final_bot.py.
     */
    public function handle(Request $request)
    {
        return response()->json(['status' => 'Python bot is handling the logic']);
    }
}