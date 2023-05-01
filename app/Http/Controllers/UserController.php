<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponsApi;


class UserController extends Controller 
{
    use ResponsApi;

    public function register(Request $request)
    {  
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'school_id' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:255',
            'confirm_password' => 'required|string|same:password|min:8|max:255',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            // Jika nomor telepon sudah terdaftar, kirim response dengan pesan error
            return $this->badRequest('Nomor telepon sudah terdaftar. Silahkan gunakan nomor telepon yang lain');
        }

        if ($validator->fails()){
            return $this->responseValidation($validator->errors(), 'register gagal, silahkan coba kembali');
        }

        $request['password'] = bcrypt($request['password']);
        $user = User::create($request->all());

        return redirect()->route('index');
    }

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