<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LinkController extends Controller
{
    public function getAll()
    {
        try {
            $links = Link::all();
            return $this->successResponse($links);
        } catch (\Exception $exception) {
            return $this->internalServerErrorResponse($exception);
        }
    }

    public function getByCode($code)
    {
        try {
            $link = Link::where('code', $code)->firstOrFail();
            return $this->successResponse($link);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('Link with given code is not found');
            } else {
                return $this->internalServerErrorResponse($exception);
            }
        }
    }

    public function updateLink(Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required',
                'code' => 'required',
                'value' => 'required',
            ]);

            $link = Link::find($request->id);
            if (!$link) {
                throw new ModelNotFoundException();
            }
            $link->code = $request->code;
            $link->value = $request->value;
            $link->save();
            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } else if ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('Link not found');
            } else {
                return $this->internalServerErrorResponse($exception);
            }
        }
    }

    public function massUpdateLink(Request $request)
    {
        try {
            $this->validate($request, [
                'links' => 'required|array',
            ]);

            foreach ($request->links as $item) {
                $link = Link::find($item['id']);

                if (!$link) {
                    continue;
                }

                $link->code = $item['code'];
                $link->value = $item['value'];
                $link->save();
            }

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
