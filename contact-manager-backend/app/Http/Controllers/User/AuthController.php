<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User;
    }

    public function register(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'first_name'    => 'required|string',
                'last_name'     => 'required|string',
                'email'         => 'required|email',
                'password'      => 'required|string|min:6'
            ]
        );

        if($validation->fails())
        {
            return response()->json([
                "success" => false,
                "message" => $validation->errors()->toArray()
            ], 400);
        }

        $check_email = $this->user->where("email",$request->email)->count();
        if($check_email > 0){
            return response()->json([
                "success"   => false,
                "message"   => "This email has already registered"
            ], 200);
        }

        $registered_user = $this->user::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'password'      => \Hash::make($request->password)
        ]);

        if($registered_user){
            return $this->login($request);
        }

        return response()->json([
            'success'   => false,
            'message'   => "Error encountered, please try again."
        ]);

    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->only('email','password'),[
            'email'         => 'required|email',
            'password'      => 'required|string|min:6'
        ]);

        if($validation->fails()){
            return response()->json([
                "success"   => false,
                "message"   => $validation->errors()->toArray()
            ],400);
        }

        $jwt_token = null;

        $input = $request->only('email','password');

        if(!$jwt_token = auth('users')->attempt($input)){
            return response()->json([
                "success"   => false,
                "message"   => "Invalid credentials"
            ]);
        }

        return response()->json([
            'success'   => true,
            'token'     => $jwt_token
        ]);
    }
}
