<?php

namespace App\Http\Controllers;

use App\Mail\EmailConfirmation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $customClaims = ['role' => 'user'];
            $token = app('auth')->claims($customClaims)->attempt($request->only('email', 'password'));
            if ($token) {
                $user = User::where('email', $request->email)->first();
                $json_user = $user->toArray();
                $json_user['jwt_token'] = $token;
                return response()->json(['code' => '200', 'data' => $json_user]);
            } else {
                return response()->json(['code' => '401', 'message' => 'Your email or password is wrong']);
            }
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } elseif ($exception instanceof JWTException) {
                return response()->json(['code' => '500', 'message' => 'Internal server error'], 500);
            }
        }
    }

    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'school_name' => 'require',
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Check email already taken or not
            $email_taken = User::firstWhere('email', $request->email);
            if ($email_taken) {
                return response()->json(['code' => '409', 'message' => 'Email already taken'], 409);
            }

            // Generate token for email verification
            $token = random_bytes(8);
            $token = bin2hex($token);

            // Create new user data
            $user = User::create($request->all());
            $user->email_verify_token = $token;
            $user->save();

            // Send verification email
            $data = array('name' => $request->name, 'email' => $request->email, 'token' => $token);
            Mail::to($request->email)->send(new EmailConfirmation((object) $data));
            return response()->json(['code' => '201', 'message' => 'Registration is successful, please verify your email'], 201);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            }
        }
    }
}
