<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
    public function unexpectedError()
    {
        return response()->json(['code' => '500', 'message' => 'Unexpected error']);
    }

    public function getAll()
    {
        $announcements = Announcement::all();
        return response()->json(['code' => 200, 'data' => $announcements]);
    }

    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                'title' => 'required|string',
                'content' => 'required|string',
                'show' => 'required|boolean'
            ]);

            $announcement = new Announcement;
            $announcement->title = $request->title;
            $announcement->content = $request->content;
            $announcement->show = $request->show;
            $announcement->save();

            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else $this->unexpectedError();
        }
    }

    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required',
                'title' => 'required|string',
                'content' => 'required|string',
                'show' => 'required|boolean'
            ]);

            $announcement = Announcement::find($request->id);
            if (!$announcement) {
                throw new ModelNotFoundException();
            }
            $announcement->title = $request->title;
            $announcement->content = $request->content;
            $announcement->show = $request->show;
            $announcement->save();

            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else if ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => '404', 'message' => 'Announcement not found'], 404);
            } else $this->unexpectedError();
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

            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => 404, 'message' => 'Announcement not found'], 404);
            } else $this->unexpectedError();
        }
    }
}
