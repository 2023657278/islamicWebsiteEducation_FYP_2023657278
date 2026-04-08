<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TelegramController extends Controller
{
    /**
     * Focus: The Handshake
     * This method is called when the student clicks "I Have Sent The Code" 
     * on the website to verify their connection.
     */
    public function verify(Request $request)
    {
        // 1. Get the currently logged-in student
        $user = auth()->user();

        // 2. Refresh data from the database 
        // (Checks if final_bot.py has filled the telegram_chat_id yet)
        $user->refresh();

        if ($user->telegram_chat_id) {
            return redirect()->route('student.profile')->with('success', 'Telegram connected successfully!');
        }

        return redirect()->back()->with('error', 'We haven\'t received your code on Telegram yet. Please search for @PAI_MRSM_bot and send your code.');
    }

    /**
     * Optional: Webhook endpoint
     * Only needed if you want Laravel to receive messages. 
     * Since you use final_bot.py (Polling), this is technically not required.
     */
    public function handle(Request $request)
    {
        return response()->json(['status' => 'Python bot is handling the logic']);
    }
}