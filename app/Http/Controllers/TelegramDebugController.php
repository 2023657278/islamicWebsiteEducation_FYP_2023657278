<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class TelegramDebugController extends Controller
{
    // Debug: Check if a user has telegram_chat_id
    public function checkUsers()
    {
        $allUsers = User::all(['id', 'name', 'email', 'telegram_chat_id']);
        
        echo "<h2>User Telegram Status</h2>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Telegram Chat ID</th><th>Status</th></tr>";
        
        foreach ($allUsers as $user) {
            $status = $user->telegram_chat_id ? "✅ Connected" : "❌ Not Linked";
            echo "<tr>";
            echo "<td>{$user->id}</td>";
            echo "<td>{$user->name}</td>";
            echo "<td>{$user->email}</td>";
            echo "<td>" . ($user->telegram_chat_id ?? 'NULL') . "</td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        Log::info('Telegram Debug Check Complete', $allUsers->toArray());
    }

    // Debug: Simulate webhook to test if bot recognizes a chat_id
    public function testChatId($chatId)
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        
        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'User found',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telegram_chat_id' => $user->telegram_chat_id
                ]
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'User NOT found for this telegram_chat_id',
            'searched_chat_id' => $chatId
        ], 404);
    }

    // Debug: Force link a user (be careful with this!)
    public function linkUserDebug($userId, $chatId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        $user->telegram_chat_id = $chatId;
        $user->save();
        
        Log::info('Force Linked Telegram', [
            'user_id' => $userId,
            'telegram_chat_id' => $chatId
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'User linked manually for debugging',
            'user' => $user
        ]);
    }
}
