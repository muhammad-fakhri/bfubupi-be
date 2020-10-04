<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PaperController extends Controller
{
    public function getAllPaper()
    {
        $papers = Paper::all();
        return response()->json(['code' => '200', 'data' => $papers]);
    }

    public function checkPaper(Request $request)
    {
        $paper = Paper::find($request->get('paper_id'));
        $paper->is_checked = true;
        $paper->save();
        return response()->json(['code' => '200', 'message' => 'Success']);
    }

    public function getPaperByUser(Request $request)
    {
        $user = User::find($request->get('user_id'));
        $papers = $user->papers;
        return response()->json(['code' => '200', 'data' => $papers]);
    }

    public function uploadPaper(Request $request)
    {
        try {
            $this->validate($request, [
                'user_id' => 'required|exists:users,id',
                'paper_title' => 'required',
                'paper_file' => 'required|mimes:pdf,docx',
            ]);

            $file = $request->file('paper_file');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $unique_name = md5($filename . time());
            $path = $file->storeAs('paper', $unique_name . '.' . $extension);

            $user = User::find($request->user_id);
            $paper = $user->papers()->create([
                'paper_title' => $request->input('paper_title'),
                'paper_file_name' => $unique_name . '.' . $extension,
                'paper_file_path' => $path
            ]);
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else {
                return response()->json(['code' => '500', 'message' => 'Internal server error'], 500);
            }
        }
    }

    public function deletePaper(Request $request)
    {
        try {
            $this->validate($request, [
                'paper_id' => 'required|exists:paper,id'
            ]);
            $paper = Paper::find($request->paper_id);
            Storage::delete($paper->paper_file_path);
            $paper->delete();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            }
        }
    }
}
