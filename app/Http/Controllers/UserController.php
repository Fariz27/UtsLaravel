<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $akun = $request->only('username', 'pin');
        try {
            if (! $token = JWTAuth::attempt($akun)) {
                return response()->json(['error' => 'akun_salah'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'tidak_dapat_membuat_token'], 500);
        }
        return response()->json(compact('token'));
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'pin' => 'required|min:6|confirmed',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'pin' => Hash::make($request->get('pin')),
            'jmlSaldo' => 0,
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'));
    }
}