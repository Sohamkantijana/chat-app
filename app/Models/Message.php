<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'file_path',     // NEW
        'file_type',     // NEW
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function isImage()
    {
        return $this->file_type === 'image';
    }

    public function isFile()
    {
        return $this->file_type === 'file';
    }
}
