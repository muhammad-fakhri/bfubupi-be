<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{

    public function getAllSubadmin()
    {
        try {
            $admins = Admin::all();
            return $this->successResponse($admins);
        } catch (\Exception $exception) {
            return $this->internalServerErrorResponse($exception);
        }
    }

    public function getAdminProfile()
    {
        try {
            $token = JWTAuth::getToken();
            $apy = JWTAuth::getPayload($token);
            $admin = Admin::find($apy['sub']);
            return $this->successResponse($admin);
        } catch (\Exception $exception) {
            return $this->internalServerErrorResponse($exception);
        }
    }

    public function createSubadmin(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:admins,email',
                'password' => 'required',
            ]);
            $admin = new Admin;
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password);
            $admin->save();
            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } else {
                return $this->internalServerErrorResponse($exception);
            }
        }
    }

    public function updateSubadmin(Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:admins,email,' . $request->id,
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
            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } else if ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('Subadmin not found');
            } else {
                return $this->internalServerErrorResponse($exception);
            }
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

            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } else {
                return $this->internalServerErrorResponse($exception);
            }
        }
    }
}
