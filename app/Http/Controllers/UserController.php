<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getAllUser()
    {
        try {
            $users = User::all();
            return $this->successResponse($users);
        } catch (\Exception $exception) {
            return $this->internalServerErrorResponse($exception);
        }
    }

    public function getUserProfile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return $this->successResponse($user);
        } catch (\Exception $exception) {
            $this->internalServerErrorResponse($exception);
        }
    }

    public function updateUserProfile(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'school_name' => 'required'
            ]);

            $user = JWTAuth::parseToken()->authenticate();
            $user->name = $request->name;
            $user->school_name = $request->school_name;
            $user->save();
            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } else if ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('User not found');
            } else $this->internalServerErrorResponse($exception);
        }
    }
}
