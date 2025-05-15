<?php

namespace App\Http\Controllers\chat;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($recipient_id)
    {
        $userId = Auth::id();

        $chats = Chat::where(function ($query) use ($userId, $recipient_id) {
            $query->where('sender_id', $userId)->where('recipient_id', $recipient_id);
        })
            ->orWhere(function ($query) use ($userId, $recipient_id) {
                $query->where('sender_id', $recipient_id)->where('recipient_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($chats);
    }

    // POST /api/chats
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Message sent', 'data' => $chat], 201);
    }

    // DELETE /api/chats/{id}
    public function destroy($id)
    {
        $chat = Chat::findOrFail($id);

        if ($chat->sender_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $chat->delete();

        return response()->json(['message' => 'Chat deleted']);
    }
}
