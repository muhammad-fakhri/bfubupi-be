<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function getAllPayment()
    {
        $payments = Payment::all();
        return response()->json(['code' => '200', 'data' => $payments]);
    }

    public function checkPayment(Request $request)
    {
        $payment = Payment::find($request->get('payment_id'));
        $payment->is_checked = true;
        $payment->save();
        return response()->json(['code' => '200', 'message' => 'Success']);
    }

    public function getPaymentByUser(Request $request)
    {
        $user = User::find($request->get('user_id'));
        $payments = $user->payments;
        return response()->json(['code' => '200', 'data' => $payments]);
    }

    public function uploadPayment(Request $request)
    {
        try {
            $this->validate($request, [
                'user_id' => 'required|exists:users,id',
                'payment_title' => 'required',
                'payment_receipt_image' => 'required|mimes:png,jpg',
            ]);

            $image = $request->file('payment_receipt_image');
            $filename = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $unique_name = md5($filename . time());
            $path = $image->storeAs('payment', $unique_name . '.' . $extension);

            $user = User::find($request->user_id);
            $payment = $user->payments()->create([
                'payment_title' => $request->input('payment_title'),
                'payment_description' => $request->input('payment_description', null),
                'payment_file_name' => $unique_name . '.' . $extension,
                'payment_file_path' => $path
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

    public function deletePayment(Request $request)
    {
        try {
            $this->validate($request, [
                'payment_id' => 'required|exists:payment,id'
            ]);
            $payment = Payment::find($request->payment_id);
            Storage::delete($payment->payment_file_path);
            $payment->delete();
            return response()->json(['code' => '200', 'message' => 'Success']);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                return response()->json(['code' => '400', 'message' => 'Bad request'], 400);
            }
        }
    }
}
