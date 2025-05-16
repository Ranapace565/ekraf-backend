<?php

namespace App\Http\Controllers\chat;

use App\Models\Chat;
use App\Models\ChatThread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();
        $authId = $authUser->id;

        // Ambil semua thread di mana user terlibat
        $threads = ChatThread::with(['entrepreneur', 'visitor', 'messages' => function ($q) {
            $q->latest()->limit(1); // ambil pesan terakhir
        }])
            ->where('entrepreneur_id', $authId)
            ->orWhere('visitor_id', $authId)
            ->latest('updated_at')
            ->get();

        if ($threads->isEmpty()) {
            return response()->json(['message' => 'Anda belum memulai percakapan'], 200);
        }

        // Format output
        $formatted = $threads->map(function ($thread) use ($authId) {
            $lastMessage = $thread->messages->first();
            $partner = $thread->entrepreneur_id === $authId ? $thread->visitor : $thread->entrepreneur;

            return [
                'thread_id' => $thread->id,
                'partner_name' => $partner->name,
                'partner_id' => $partner->id,
                'last_message' => $lastMessage ? $lastMessage->message : null,
                'last_sender_id' => $lastMessage ? $lastMessage->sender_id : null,
                'last_message_read' => $lastMessage ? $lastMessage->is_read : null,
                'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
            ];
        });

        return response()->json($formatted);
    }


    public function show($id)
    {
        $authId = Auth::id();

        $thread = ChatThread::with(['messages' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->find($id);

        // Cek apakah thread ditemukan
        if (!$thread || ($thread->entrepreneur_id !== $authId && $thread->visitor_id !== $authId)) {
            return response()->json(['message' => 'Anda tidak memiliki akses percakapan ini'], 403);
        }

        // Tandai pesan dari lawan bicara sebagai telah dibaca
        Chat::where('thread_id', $id)
            ->where('sender_id', '!=', $authId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'thread_id' => $thread->id,
            'messages' => $thread->messages
        ]);
    }



    // POST /api/chats
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $authId = Auth::id();
        $recipientId = $request->recipient_id;

        // Cek apakah thread sudah ada
        $thread = ChatThread::where(function ($q) use ($authId, $recipientId) {
            $q->where('entrepreneur_id', $authId)->where('visitor_id', $recipientId);
        })->orWhere(function ($q) use ($authId, $recipientId) {
            $q->where('entrepreneur_id', $recipientId)->where('visitor_id', $authId);
        })->first();

        // Jika belum, buat thread baru
        if (!$thread) {
            // Cek role pengguna aktif dan recipient
            $auth = Auth::user();
            $isEntrepreneur = $auth->role === 'entrepreneur';

            $thread = ChatThread::create([
                'entrepreneur_id' => $isEntrepreneur ? $authId : $recipientId,
                'visitor_id' => $isEntrepreneur ? $recipientId : $authId,
            ]);
        }

        // Simpan pesan
        $message = Chat::create([
            'thread_id' => $thread->id,
            'sender_id' => $authId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Message sent',
            'data' => $message,
            'thread_id' => $thread->id
        ], 201);
    }

    // DELETE /api/chats/messages/{id}
    public function destroy($id)
    {
        $message = Chat::findOrFail($id);

        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted']);
    }

    // GET /api/chats/unread-count
    public function unreadCount()
    {
        $authId = Auth::id();

        $count = Chat::where('is_read', false)
            ->where('sender_id', '!=', $authId)
            ->whereHas('thread', function ($q) use ($authId) {
                $q->where('entrepreneur_id', $authId)
                    ->orWhere('visitor_id', $authId);
            })->count();

        return response()->json(['unread' => $count]);
    }
}
