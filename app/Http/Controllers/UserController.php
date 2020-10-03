<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $users = User::all();
        return response()->json(['code' => '200', 'data' => $users]);
    }

    public function getUserProfile($user_id)
    {
        try {
            $user = User::find($user_id);
            return response()->json(['code' => '200', 'data' => $user]);
        } catch (\Exception $exception) {
            return response()->json(['code' => '500', 'message' => 'Internal server error'], 500);
        }
    }

    public function updateUserProfile(Request $request, $user_id)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'school_name' => 'required'
            ]);

            $user = User::find($user_id);
            $user->name = $request->name;
            $user->school_name = $request->school_name;
            $user->save();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad Request'], 400);
            } else {
                return response()->json(['code' => '500', 'message' => 'Internal server error'], 500);
            }
        }
    }
}
