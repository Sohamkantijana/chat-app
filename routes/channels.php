<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{u1}.{u2}', function ($user, $u1, $u2) {
    return (int)$user->id === (int)$u1 || (int)$user->id === (int)$u2;
});

