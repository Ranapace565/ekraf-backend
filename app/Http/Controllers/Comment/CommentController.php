<?php

namespace App\Http\Controllers\Comment;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index($business_id)
    {
        $user = Auth::user();

        $comments = Comment::where('business_id', $business_id)
            ->with('user:id,email,role')
            ->get()
            ->map(function ($comment) use ($user) {
                return [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'user_role' => $comment->user->role ?? null,
                    // 'user_name' => $comment->user->name ?? null,
                    'user_email' => $comment->user->email ?? null,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                    'is_owner' => $user ? $comment->user_id === $user->id : false,
                ];
            })
            ->sortByDesc('is_owner')
            ->values();

        return response()->json([
            'comments' => $comments
        ]);
    }

    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 401);
        }

        $comments = Comment::where('user_id', $user->id)
            ->with('business:id,name') // ambil info nama usaha sekalian kalau mau
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'business_id' => $comment->business_id,
                    // 'business_name' => $comment->business->name ?? null,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                ];
            });

        return response()->json([
            'comments' => $comments
        ]);
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'comment' => 'required|string',
        ]);

        $existingComment = Comment::where('user_id', $user->id)
            ->where('business_id', $validated['business_id'])
            ->first();

        if ($existingComment) {
            return response()->json([
                'message' => 'Kamu suda mengulas usaha ini.'
            ], 409); // 409 Conflict
        }

        $comment = Comment::create([
            'user_id' => $user->id,
            'business_id' => $validated['business_id'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'message' => 'Comment created successfully.',
            'comment' => $comment,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'Comment tidak ditemukan.'
            ], 404);
        }

        if ($comment->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized. Kamu tidak dapat mengubah komentar orang lain.'
            ], 403);
        }

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->comment = $validated['comment'];
        $comment->save();

        return response()->json([
            'message' => 'Comment updated successfully.',
            'comment' => $comment,
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found.'
            ], 404);
        }

        if ($comment->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized. You can only delete your own comment.'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.'
        ]);
    }
}
