<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $data['token'] = Auth::user()->createToken('nApp')->accessToken;
            $data['uid'] = Auth::user()->uid;
            $data['name'] = Auth::user()->name;
            return response()->json(['error' => false, 'message' => 'Login success !', 'data' => $data]);
        }
        return response()->json(['error' => true, 'message' => 'Account not found !']);
    }
}
