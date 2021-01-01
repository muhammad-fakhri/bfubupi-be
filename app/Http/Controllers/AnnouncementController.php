<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
    public function getAll()
    {
        try {
            $announcements = Announcement::all();
            return $this->successResponse($announcements);
        } catch (\Exception $exception) {
            return $this->internalServerErrorResponse($exception);
        }
    }

    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                'link' => 'required|string',
                'content' => 'required|string',
                'show' => 'required|boolean',
            ]);

            $announcement = new Announcement;
            $announcement->link = $request->link;
            $announcement->content = $request->content;
            $announcement->show = $request->show;
            $announcement->save();

            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } else {
                return $this->internalServerErrorResponse($exception);
            }
        }
    }

    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required',
                'link' => 'required|string',
                'content' => 'required|string',
                'show' => 'required|boolean',
            ]);

            $announcement = Announcement::find($request->id);
            if (!$announcement) {
                throw new ModelNotFoundException();
            }
            $announcement->link = $request->link;
            $announcement->content = $request->content;
            $announcement->show = $request->show;
            $announcement->save();

            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } else if ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('Announcement not found');
            } else {
                return $this->internalServerErrorResponse($exception);
            }
        }
    }

    public function delete($id)
    {
        try {
            $announcement = Announcement::find($id);
            if (!$announcement) {
                throw new ModelNotFoundException();
            }
            $announcement->delete();

            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('Announcement not found');
            } else {
                return $this->internalServerErrorResponse($exception);
            }
        }
    }
}
