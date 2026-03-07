<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Mutfak ekranı WebSocket kanalı — sadece o kullanıcı dinleyebilir
Broadcast::channel('kitchen.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
