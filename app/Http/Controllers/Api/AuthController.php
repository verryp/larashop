<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        
        $user = User::where('email', '=', $request->email)->firstOrFail();
        $status = 'error';
        $message = "";
        $data = null;
        $status_code = 401;

        $this->validate($request, [
            'email' => 'required:email',
            'password' => 'required'
        ]);

        if($user){
            // * cek apakah inputan pass sama dengan pass yang ada di db
            if(Hash::check($request->password, $user->password)){

                // * generate token, jadi tiap login, api_tokennya slalu berbeda
                $user->generateToken();
                $status = 'success';
                $message = 'Login sukses';

                $data = $user->toArray();
                $status_code = 200;
            }else{
                $message = "Login gagal, Password anda salah";
            }
        }else {
            $message = 'Login gagal, username anda salah';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }

    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'username' => 'required|unique:users'
        ]);

        if($validator->fails()){
            $errors = $validator->errors();

            return response()->json([
                'data' => [
                    'message' => $errors,
                ]
            ], 400);
        }else{
           $user = User::create([
               'name' => $request->name,
               'email' => $request->email,
               'password' => $request->password,
               'username' => $request->username,
               'roles' => json_encode(['CUSTOMER']),
               'status' => 'ACTIVE',
               'address' => $request->address,
               'phone' => $request->phone
           ]);

           if($user){
               $user->generateToken();
               $status = 'success';
               $message = 'Registrasi berhasil';
               $data = $user->toArray();
               $status_code = 400;
           }else{
               $message = 'Registrasi anda gagal';
           }
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }

    public function logout(Request $request){
        $user = Auth::guard('api')->user();

        if($user){
            $user->api_token = null;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
            'data' => null
        ], 200);
    }
}
