<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponsApi;


class AdminController extends Controller 
{
    use ResponsApi;

    public function login()
    {
        $input = request(['username', 'password']);

        if (!$token = auth("api")->attempt($input)) {
            return response()->json(['message' => 'Nomor Telepon atau Kata Sansi yang anda masukan tidak valid, silahkan coba lagi'], 401);
        }

        redirect()->route('home');
    }

    public function register(Request $request)
    {  
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:255',
            'confirm_password' => 'required|string|same:password|min:8|max:255',
        ]);

        $user = Admin::where('username', $request->username)->first();

        if ($user) {
            // Jika nomor telepon sudah terdaftar, kirim response dengan pesan error
            return $this->badRequest('Username sudah terdaftar. Silahkan gunakan usernmae yang lain');
        }

        if ($validator->fails()){
            return $this->responseValidation($validator->errors(), 'register gagal, silahkan coba kembali');
        }

        $request['password'] = bcrypt($request['password']);
        $user = Admin::create($request->all());
    }
}