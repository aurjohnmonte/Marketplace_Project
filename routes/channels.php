<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel("message.{username}", function($user, $username){
    return true;
});

Broadcast::channel("active-notify", function($user){
    return true;
});

Broadcast::channel("notify", function(){
    return true;
});

Broadcast::channel("sellernotify.{username}", function($user, $username){
    return true;
});

