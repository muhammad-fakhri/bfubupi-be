<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\throwException;

class AdminController extends Controller
{
    public function getAllSubadmin()
    {
        $admins = Admin::all();
        return response()->json(['code' => '200', 'data' => $admins]);
    }

    public function createSubadmin(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:admins,email',
                'password' => 'required'
            ]);
            $admin = new Admin;
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password);
            $admin->save();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            }
        }
    }


    public function updateSubadmin(Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required',
                'name' => 'required',
                'email' => 'required|email',
            ]);

            $admin = Admin::find($request->id);
            if (!$admin) {
                throw new ModelNotFoundException();
            }
            $admin->name = $request->name;
            $admin->email = $request->email;
            if ($request->has('password')) {
                $admin->password = Hash::make($request->password);
            }
            $admin->save();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
        }
    }

    public function deleteSubadmin(Request $request)
    {
        try {
            $this->validate($request, [
                'admin_id' => 'required',
            ]);

            $admin = Admin::find($request->admin_id);
            if (!$admin) {
                throw new ModelNotFoundException();
            }
            $admin->delete();

            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
        }
    }
}
