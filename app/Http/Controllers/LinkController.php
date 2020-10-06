<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
}
