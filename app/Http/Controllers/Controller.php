<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    function baseResponse($code, $message, $data)
    {
        return response()->json(['code' => $code, 'message' => $message, 'data' => $data], $code);
    }

    function baseErrorResponse($code, $message, $error)
    {
        return response()->json(['code' => $code, 'message' => $message, 'error' => $error], $code);
    }

    public function successResponse($data = null, $message = 'Success')
    {
        return $this->baseResponse(200, $message, $data);
    }

    public function createdResponse($data = null, $message = 'Success created')
    {
        return $this->baseResponse(201, $message, $data);
    }

    public function internalServerErrorResponse(\Exception $exception, $message = 'Unexpected Error')
    {
        return $this->baseErrorResponse(500, $message, $exception->getMessage());
    }

    public function badRequestResponse(\Exception $exception, $message = 'Bad Request')
    {
        return $this->baseErrorResponse(400, $message, $exception->getMessage());
    }

    public function unauthorizedResponse($message = 'Unauthorized')
    {
        return $this->baseErrorResponse(401, $message, null);
    }

    public function forbiddenResponse($message = 'Forbidden')
    {
        return $this->baseErrorResponse(403, $message, null);
    }

    public function notFoundResponse($message = 'Resource not found')
    {
        return $this->baseErrorResponse(404, $message, null);
    }

    public function conflictResponse($message = 'Conflict')
    {
        return $this->baseErrorResponse(409, $message, null);
    }

    public function unprocessableEntityResponse($message = 'Unprocessable entity')
    {
        return $this->baseErrorResponse(422, $message, null);
    }
}
