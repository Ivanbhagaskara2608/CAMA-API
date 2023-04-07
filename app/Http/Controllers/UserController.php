<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{   
    public function index(Request $request)
    {
        // return response()->json([
        //    Auth::user()
        // ]);
        return $request->user();
    }

    public function register(Request $request)
    {
        $validation = $request->validate([
            'fullname' => 'required|max:64',
            'username' => 'required|min:4|max:16',
            'password' => 'required|min:6|max:32'
        ]);

        $validation['password'] = bcrypt($validation['password']);
        $result = User::create($validation);
        return response()->json([
            "message" => "Registration succeeded",
            "data" => $result
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = md5(time()) . '.' . md5($request->fullname);
            $user->forceFill([
                'token' => $token
            ])->save();
            return response()->json([
                "message" => "Login Succeeded",
                "token" => $token
            ]);
        };

        return response()->json([
            "message" => "Login Failed"
        ]);

    }

    public function logout(Request $request)
    {
        // clean api_token from database
        $request->user()->forceFill([
            'token' => null
        ])->save();
        
        // return result
        return response()->json(['message'=>'success']);
    }
}
