<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat.index', compact('users'));
    }

    public function fetchMessages($userId)
    {
        return Message::where(function ($q) use ($userId) {
            $q->where('sender_id', Auth::id())
              ->where('receiver_id', $userId);
        })
        ->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->where('receiver_id', Auth::id());
        })
        ->orderBy('created_at')
        ->get();
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:51200' // 50 MB
        ]);

        $filePath = null;
        $fileType = 'text'; // default message type

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $mime = $file->getMimeType();

            if (str_contains($mime, 'image')) {
                $fileType = 'image';
            } else {
                $fileType = 'file';
            }

            // store file inside storage/app/public/chat_files
            $filePath = $file->store('chat_files', 'public');
        }

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'file_path' => $filePath,
            'file_type' => $fileType,
        ]);

        broadcast(new MessageSent($msg))->toOthers();

        return response()->json($msg);
    }
}
