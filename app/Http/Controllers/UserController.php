<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponsApi;
use Illuminate\Support\Facades\Auth;

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

        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'register gagal, silahkan coba kembali');
        }

        $request['password'] = bcrypt($request['password']);
        $user = User::create($request->all());

        return redirect()->route('index');
    }

    function sendNotification (Request $request) {
  
        $id = $request['user_id'];
   
        // get a user to get the fcm_token that already sent from mobile apps 
        $user = User::findOrFail($id);

        $user = User::where('id', $id)->first();

        $deviceToken = $user->device_token;

          $data = [
               'title' => $request->input('title'),
               'body' => $request->input('body'),
                'sent to' => $user->name,
          ];
   
      if (is_null($request->input('title')) || is_null($request->input('body'))) {
      
      $data['title'] = $user->name;
          $data['body'] = ('Anda telah dicolek '.$id->name);
          
      }
      
      $headers = [
       'Authorization: key=AAAA_ByAGGM:APA91bF3L2INa9vdyBnJYCHhr7orjT7OgpIizm3UZlJUNEkhoTODCPeO1KmTWyCvsWN1C1GV05FrMHMtdqCockid6jMkfi2SxXr9ku80iU9E6NZF_q11lWUSol_rKoo77iNeE1zjtsgs',
       'Content-Type: application/json',
   ];
   
      $options = [
          CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
          CURLOPT_POST => true,
          CURLOPT_HTTPHEADER => $headers,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS => json_encode([
              'to' => $deviceToken,
              'data' => $data,
          ]),
      ];
   
      $ch = curl_init();
      curl_setopt_array($ch, $options);
   
      $response = curl_exec($ch);
   
      curl_close($ch);
      if ($response === false) {
          die('cURL error: ' . curl_error($ch));
      }
   
      $responseData = json_decode($response, true);
   
      if (isset($responseData['error'])) {
          die('FCM API error: ' . $responseData['error']);
      }
      return $this->requestSuccessData('Successfully sent', $data );     
        }
   
   //NOTIF BENAYA\\
   
    function benNotify (Request $request, $user_id) {
     
        $id = $request['user_id'];
        $user = User::findOrFail($id);
        $user = User::where('id', $id)->first();

        $deviceToken = $user->device_token;

          $data = [
               'title' => $request->input('title'),
               'body' => $request->input('body'),
                'sent to' => $user->name,
          ];
   
       if (is_null($request->input('title')) || is_null($request->input('body'))) {
       
       $data['title'] = $user->nama;
           $data['body'] = ('Anda telah dicolek '.$id->nama);
           
       }
       
           $headers = [
               'Authorization: key=AAAA_ByAGGM:APA91bF3L2INa9vdyBnJYCHhr7orjT7OgpIizm3UZlJUNEkhoTODCPeO1KmTWyCvsWN1C1GV05FrMHMtdqCockid6jMkfi2SxXr9ku80iU9E6NZF_q11lWUSol_rKoo77iNeE1zjtsgs',
               'Content-Type: application/json',
           ];
   
   
       $options = [
           CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
           CURLOPT_POST => true,
           CURLOPT_HTTPHEADER => $headers,
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_POSTFIELDS => json_encode([
               'to' => $deviceToken,
               'data' => $data,
           ]),
       ];
   
       $ch = curl_init();
       curl_setopt_array($ch, $options);
   
       $response = curl_exec($ch);
   
       curl_close($ch);
       if ($response === false) {
           die('cURL error: ' . curl_error($ch));
       }
   
       $responseData = json_decode($response, true);
   
       if (isset($responseData['error'])) {
           die('FCM API error: ' . $responseData['error']);
       }
       return $this->requestSuccessData('Successfully sent', $data );             
    }
   
}
