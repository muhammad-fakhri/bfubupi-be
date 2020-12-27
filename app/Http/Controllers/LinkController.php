<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class LinkController extends Controller
{
    public function unexpectedError()
    {
        return response()->json(['code' => '500', 'message' => 'Unexpected error']);
    }

    public function getAll()
    {
        $links = Link::all();
        return response()->json(['code' => 200, 'data' => $links]);
    }

    public function getByCode($code)
    {
        try {
            $link = Link::where('code', $code)->firstOrFail();
            return response()->json(['code' => 200, 'data' => $link]);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => 404, 'message' => 'Link with given code is not found'], 404);
            } else $this->unexpectedError();
        }
    }

    public function updateLink(Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required|exists:links,id',
                'code' => 'required',
                'value' => 'required'
            ]);

            $link = Link::find($request->id);
            if (!$link) {
                throw new ModelNotFoundException();
            }
            $link->code = $request->code;
            $link->value = $request->value;
            $link->save();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else if ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => '404', 'message' => 'Link not found'], 404);
            } else $this->unexpectedError();
        }
    }
}
