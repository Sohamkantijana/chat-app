<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // Changed from ShouldBroadcast
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow  // Changed from ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    // Use consistent ordering (smaller id first) to match channels.php
    public function broadcastOn()
    {
        $u1 = (int)$this->message->sender_id;
        $u2 = (int)$this->message->receiver_id;

        $first = min($u1, $u2);
        $second = max($u1, $u2);

        return new PrivateChannel("chat.{$first}.{$second}");
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'message' => $this->message->message,
            'file_path' => $this->message->file_path,
            'file_type' => $this->message->file_type,
            'created_at' => $this->message->created_at->toDateTimeString()
        ];
    }
}