<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\FCMService;

class UserController extends Controller 
{
    public function sendNotification($id)
    {
       // get a user to get the fcm_token that already sent from mobile apps 
       $user = User::findOrFail($id);

       FCMService::send(
          $user->fcm_token,
          [
              'title' => 'Hello',
              'body' => 'Good Morning',
          ]
      );

    }
}