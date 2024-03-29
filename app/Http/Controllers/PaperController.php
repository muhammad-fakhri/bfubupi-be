<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class PaperController extends Controller
{
    public function getAllPaper()
    {
        try {
            $papers = Paper::all();
            return $this->successResponse($papers);
        } catch (\Exception $exception) {
            return $this->internalServerErrorResponse($exception);
        }
    }

    public function checkPaper($paper_id)
    {
        try {
            $paper = Paper::findOrFail($paper_id);
            $paper->is_checked = true;
            $paper->save();
            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return $this->badRequestResponse($exception);
            } else $this->internalServerErrorResponse($exception);
        }
    }

    public function getPaperByUser($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $papers = $user->papers;
            return $this->successResponse($papers);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return $this->badRequestResponse($exception);
            } else $this->internalServerErrorResponse($exception);
        }
    }

    public function uploadPaper(Request $request)
    {
        try {
            $this->validate($request, [
                'user_id' => 'required|exists:users,id',
                'paper_title' => 'required',
                'paper_file' => 'required|mimes:pdf,docx',
            ]);

            // Find the user
            $user = User::findOrFail($request->user_id);

            // Generate file name and destination path
            $file = $request->file('paper_file');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $path = 'uploads' . DIRECTORY_SEPARATOR . 'paper' . DIRECTORY_SEPARATOR;
            $destinationPath = public_path($path);
            File::makeDirectory($destinationPath, 0777, true, true);

            // Move file to destination path with generated filename
            $file->move($destinationPath, $filename);

            $paper = $user->papers()->create([
                'paper_title' => $request->input('paper_title'),
                'paper_file_name' => $filename,
                'paper_file_path' => $path . $filename
            ]);
            return $this->successResponse($paper);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } elseif ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('User not found');
            } else $this->internalServerErrorResponse($exception);
        }
    }

    public function deletePaper(Request $request)
    {
        try {
            $this->validate($request, [
                'paper_id' => 'required|exists:papers,id'
            ]);
            $paper = Paper::findOrFail($request->paper_id);

            // Delete paper file
            unlink(public_path($paper->paper_file_path));

            // Delete paper model
            $paper->delete();
            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } elseif ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('Paper not found');
            } else $this->internalServerErrorResponse($exception);
        }
    }
}
