<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function unexpectedError()
    {
        return response()->json(['code' => '500', 'message' => 'Unexpected error']);
    }

    public function getAllUser()
    {
        $users = User::all();
        return response()->json(['code' => '200', 'data' => $users]);
    }

    public function getUserProfile($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            return response()->json(['code' => '200', 'data' => $user]);
        } catch (\Exception $exception) {
            $this->unexpectedError();
        }
    }

    public function updateUserProfile(Request $request, $user_id)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'school_name' => 'required'
            ]);

            $user = User::findOrFail($user_id);
            $user->name = $request->name;
            $user->school_name = $request->school_name;
            $user->save();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad Request'], 400);
            } else if ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => '400', 'message' => 'Bad Request'], 400);
            } else $this->unexpectedError();
        }
    }
}
