<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function unexpectedError()
    {
        return response()->json(['code' => '500', 'message' => 'Unexpected error']);
    }

    public function getAllPayment()
    {
        $payments = Payment::all();
        return response()->json(['code' => '200', 'data' => $payments]);
    }

    public function checkPayment($payment_id)
    {
        try {
            $payment = Payment::findOrFail($payment_id);
            $payment->is_checked = true;
            $payment->save();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else $this->unexpectedError();
        }
    }

    public function getPaymentByUser($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $payments = $user->payments;
            return response()->json(['code' => '200', 'data' => $payments]);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else $this->unexpectedError();
        }
    }

    public function uploadPayment(Request $request)
    {
        try {
            $this->validate($request, [
                'user_id' => 'required|exists:users,id',
                'payment_title' => 'required',
                'payment_receipt_image' => 'required|mimes:png,jpeg',
            ]);

            // Find the user
            $user = User::findOrFail($request->user_id);

            // Generate file name and destination path
            $image = $request->file('payment_receipt_image');
            $filename = uniqid() . '_' . $image->getClientOriginalName();
            $path = 'uploads' . DIRECTORY_SEPARATOR . 'payment' . DIRECTORY_SEPARATOR;
            $destinationPath = public_path($path);
            File::makeDirectory($destinationPath, 0777, true, true);

            // Move file to destination path with generated filename
            $image->move($destinationPath, $filename);

            // Create new payment model attached to user model
            $payment = $user->payments()->create([
                'payment_title' => $request->input('payment_title'),
                'payment_description' => $request->input('payment_description', null),
                'payment_file_name' => $filename,
                'payment_file_path' => $path . $filename
            ]);
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } elseif ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else $this->unexpectedError();
        }
    }

    public function deletePayment(Request $request)
    {
        try {
            $this->validate($request, [
                'payment_id' => 'required|exists:payments,id'
            ]);
            $payment = Payment::findOrFail($request->payment_id);

            // Delete payment file
            unlink(public_path($payment->payment_file_path));

            // Delete payment model
            $payment->delete();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } elseif ($exception instanceof ModelNotFoundException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            } else $this->unexpectedError();
        }
    }
}
