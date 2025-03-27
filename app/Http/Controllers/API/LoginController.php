<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    function where user will be redirected for login attempt
    */
    public function login(Request $request)
    {
        $validate = Validator::make($request->input(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        if ($validate->fails()) {
            $data['message'] = $validate->errors();
            return response()->json(['status' => 'error', 'data' => $data], 422);
        }
        try {
            $user = User::with('role')->where('email', $request->input('email'))->first();
            if ($user == null) {
                $data['message'] = 'The email did not match';
                return response()->json(['status' => 'error', 'data' => $data], 401);
            }
            $check = Hash::check($request->input('password'), $user->password);

            if ($check === true) {
                if ($user->token == null) {
                    do {
                        $token = Str::random(64);
                    } while (User::where('token', $token)->exists());

                    $user->token = $token;
                    $user->save();
                }
                Auth::login($user);
                $data['user'] = $user;
                return response()->json(['status' => 'success', 'data' => $data], 200);
            } else {
                $data['message'] = 'The password did not match.';
                return response()->json(['status' => 'error', 'data' => $data], 401);
            }
        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
            return response()->json(['status' => 'error', 'data' => $data], 401);
        }
    }

    /*
    function where user will be redirected for register attempt
    */
    public function register(Request $request)
    {
        $validate = Validator::make($request->input(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);
        if ($validate->fails()) {
            $data['message'] = $validate->errors();
            return response()->json(['status' => 'error', 'data' => $data], 422);
        }
        try {
            $input = $request->input();
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'role_id' => 3
            ]);
            $data['message'] = 'User has been registered successfully';
            return response()->json(['status' => 'success', 'data' => $data], 200);
        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
            return response()->json(['status' => 'error', 'data' => $data], 401);
        }
    }
}
