<?php

namespace App\Http\Controllers;

use App\Mail\EmailConfirmation;
use App\Mail\ForgotPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function verifyEmail(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'token' => 'required'
            ]);

            $user = User::firstWhere('email', $request->get('email'));
            if ($user->is_email_verified || strcmp($user->email_verify_token, $request->get('token') != 0)) {
                return response()->json(['code' => '422', 'message' => 'Invalid token'], 422);
            }

            $user->is_email_verified = true;
            $user->email_verify_token = null;
            $user->email_verify_date = Carbon::now();
            $user->save();

            // TODO: redirect to success verify email page in frontend
            return response()->json(['code' => '200', 'message' => 'Verify success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                // TODO: redirect to failed verify email page in frontend
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else {
                // Email not found
                // TODO: redirect to failed verify email page in frontend
                return response()->json(['code' => '422', 'message' => 'Invalid token'], 422);
            }
        }
    }

    public function resendVerificationEmail(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
            ]);

            $user = User::firstWhere('email', $request->email);

            // Account not found
            if (!$user) {
                return response()->json(['code' => '404', 'message' => 'No accounts found with this email'], 404);
            }

            // Generate new email verification token
            $token = random_bytes(8);
            $token = bin2hex($token);

            $user->is_email_verified = false;
            $user->email_verify_token = $token;
            $user->email_verify_date = null;
            $user->save();

            // Resend verification email
            $data = array('name' => $request->name, 'email' => $request->email, 'token' => $token);
            Mail::to($request->email)->send(new EmailConfirmation((object) $data));

            return response()->json(['code' => '200', 'message' => 'Resend success, please check your email']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            }
        }
    }

    public function requestForgotPassword(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email'
            ]);

            $user = User::firstWhere('email', $request->email);

            // Account not found
            if (!$user) {
                return response()->json(['code' => '404', 'message' => 'No accounts found with this email'], 404);
            }

            // Generate new change password token
            $token = random_bytes(8);
            $token = bin2hex($token);

            $user->change_password_token = $token;
            $user->save();

            // Send forgot password email
            $data = array('name' => $user->name, 'email' => $request->email, 'token' => $token);
            Mail::to($request->email)->send(new ForgotPassword((object) $data));

            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            }
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'token' => 'required',
                'password' => 'required'
            ]);

            $user = User::firstWhere('email', $request->get('email'));

            // user account not exist
            if (!$user) {
                return response()->json(['code' => '422', 'message' => 'Invalid token'], 422);
            }

            if (strcmp($user->change_password_token, $request->get('token')) == 0) {
                $user->password = Hash::make($request->get('password'));
                $user->save();
            } else {
                // token mismatch
                return response()->json(['code' => '422', 'message' => 'Invalid token'], 422);
            }

            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            }
        }
    }
}
