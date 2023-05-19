<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResources;
use App\Models\User;
use App\Traits\ResponsApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    use ResponsApi;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $input = request(['phone', 'password']);
        $device_token = request('device_token');

        if (!$token = auth("api")->attempt($input)) {
            return response()->json(['message' => 'Nomor Telepon atau Kata Sansi yang anda masukan tidak valid, silahkan coba lagi'], 401);
        }

        $user = auth("api")->user();

        $data = DB::table('users')
            ->join('schools', 'users.school_id', '=', 'schools.id')
            ->select('users.id', 'users.name', 'users.phone', 'users.photo', 'users.created_at', 'users.updated_at', 'schools.id as school_id', 'schools.school_name as school_name')
            ->where('users.id', '=', $user->id)
            ->get();

        return $this->loginSuccess($data[0], $token);
    }

    public function register(Request $request)
    {
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


        return $this->requestSuccess('Register Success');
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getprofile()
    {
        $user = auth("api")->user();

        $rawData = DB::table('users')
            ->join('schools', 'users.school_id', '=', 'schools.id')
            ->select('users.id', 'users.name', 'users.photo', 'users.school_id', 'schools.school_name', 'users.phone', 'users.created_at', 'users.updated_at', 'schools.school_name as school_name')
            ->where('users.id', '=', $user->id)
            ->first(); //jangan pake get kalo get nanti dapet nya array kalo semisal first dia object dan khusus satu data 
        return $this->requestSuccessData('Success!', $rawData);
    }

    public function getfriend()
    {
        $user = auth("api")->user();

        $data = DB::table('users')
            ->join('schools', 'users.school_id', '=', 'schools.id')
            ->where('users.id', '!=', $user->id)
            ->get();

        return $this->requestSuccessData('Get Friends Success', $data);
    }
    public function getschools()
    {

        $schools = DB::table('schools')->get();

        return $this->requestSuccessData('Get Schools Success', $schools);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'image|file|max:10240'
        ]);

        if (!$request->file('photo')) {
            return $this->responseValidation($request->errors(), 'upload gagal, silahkan coba kembali');
        }
        // hapus foto sebelumnya terlebih dulu, jika ada
        $this->delete_image();

        $path = $request->file('photo')->store('profile-photo');

        $user = auth('api')->user();

        DB::table('users')
            ->where('phone', $user->phone)
            ->update(['photo' => $path]);

        return $this->requestSuccessData('Upload Success', $path);
    }

    public function delete_image()
    {
        $user = auth('api')->user();

        $file = storage_path('/app/public/public') . $user->photo;

        if (file_exists($file)) {
            @unlink($file);
        }

        $user->delete;
    }

    public function editprofile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'school_id' => 'required|string|max:255',
            'photo' => 'image|file|max:10240'
        ]);

        // get user's phone number
        $user = auth('api')->user();

        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'edit data gagal, silahkan coba kembali');
        }

        // hapus foto sebelumnya terlebih dulu, jika ada
        $this->delete_image();

        $path = $request->file('photo')->store('public', 'public');
        $link = "https://magang.crocodic.net/ki/Afifun/kelasku/storage/app/public/";
        $link .= $path;
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'name' => $request['name'],
                'school_id' => $request['school_id'],
                'photo' => $link
            ]);

        $rawData = DB::table('users')
        ->join('schools', 'users.school_id', '=', 'schools.id')
        ->select('users.id', 'users.name', 'users.photo', 'users.school_id', 'schools.school_name', 'users.phone', 'users.created_at', 'users.updated_at', 'schools.school_name as school_name')
        ->where('users.id', '=', $user->id)
        ->first();

        return $this->requestSuccessD('Edit Profile Success', $rawData);
    }

    function append_string($str1, $str2)
    {

        // Using Concatenation assignment
        // operator (.=)
        $str1 .= $str2;

        // Returning the result 
        return $str1;
    }

    public function editpassword(Request $request)
    {
        $input = request(['old_password']);

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|max:255',
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'passwor baru tidak valid, silahkan coba kembali');
        }

        $request['new_password'] = bcrypt($request['new_password']);

        // get user primary key
        $user = auth('api')->user();

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => $request['new_password'],
            ]);

        return $this->requestSuccess('Edit Password Success');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
