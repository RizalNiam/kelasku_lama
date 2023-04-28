<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponsApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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

        if (! $token = auth("api")->attempt($input)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();

        DB::table('users')
              ->where('id', '=' ,$user->id)
              ->update([
                'device_token' => $device_token,
                ]);

        return $this->loginSuccess(auth("api")->user(), $token);
    }

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

        if ($validator->fails()){
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
        response()->json(auth('api')->user());
        return $this->requestSuccessData('Get Profile Success', auth("api")->user());
    }

    public function getfriend()
    {
        $user = auth('api')->user();
        $users = DB::table('users')
                ->where('id', '!=', $user->id)
                ->get();

        return $this->requestSuccessData('Get Friend Success', $users);
    }

    public function getschools()
    {
        // cek apakah udah login
        auth('api')->user();

        $schools = DB::table('schools')->get();

        return $this->requestSuccessData('Get Schools Success', $schools);
    }

    public function upload(Request $request)
    {   
        $validateData = $request->validate([
            'photo' => 'image|file|max:10240'
        ]);

        if (!$request->file('photo')){
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

        $file = storage_path('/app/').$user['photo'];

        if (file_exists($file)){
            @unlink($file);
        }

        $user->delete;
    }

    public function editprofile(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'school_id' => 'required|string|max:255',
        ]);

        if ($validator->fails()){
            return $this->responseValidation($validator->errors(), 'edit data gagal, silahkan coba kembali');
        }

        // get user primary key
        $user = auth('api')->user();

        DB::table('users')
              ->where('phone', $user->phone)
              ->update([
                'name' => $request['name'],
                'phone' => $request['phone'],
                'school_id' => $request['school_id']
                ]);

        $user = DB::table('users')
        ->select('name', 'phone', 'school_id')->get();

        return $this->requestSuccess('Edit Profile Success');
    }

    public function editpassword(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|max:255',
            'confirm_password' => 'required|string|same:password|min:8|max:255',
        ]);

        if ($validator->fails()){
            return $this->responseValidation($validator->errors(), 'edit gagal, silahkan coba kembali');
        }

        $request['password'] = bcrypt($request['password']);

        // get user primary key
        $user = auth('api')->user();

        DB::table('users')
              ->where('phone', $user->phone)
              ->update([
                'password' => $request['password'],
                ]);

        $user = DB::table('users')
        ->select('name', 'phone', 'school_id')->get();

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