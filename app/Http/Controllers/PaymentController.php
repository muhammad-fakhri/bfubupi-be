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
    public function getAllPayment()
    {
        try {
            $payments = Payment::all();
            return $this->successResponse($payments);
        } catch (\Exception $exception) {
            return $this->internalServerErrorResponse($exception);
        }
    }

    public function checkPayment($payment_id)
    {
        try {
            $payment = Payment::findOrFail($payment_id);
            $payment->is_checked = true;
            $payment->save();
            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return $this->badRequestResponse($exception);
            } else $this->internalServerErrorResponse($exception);
        }
    }

    public function getPaymentByUser($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $payments = $user->payments;
            return $this->successResponse($payments);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return $this->badRequestResponse($exception);
            } else $this->internalServerErrorResponse($exception);
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
            return $this->successResponse($payment);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } elseif ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('User not found');
            } else $this->internalServerErrorResponse($exception);
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
            return $this->successResponse();
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return $this->badRequestResponse($exception);
            } elseif ($exception instanceof ModelNotFoundException) {
                return $this->notFoundResponse('Payment not found');
            } else $this->internalServerErrorResponse($exception);
        }
    }
}
