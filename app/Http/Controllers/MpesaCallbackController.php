<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MpesaPayment;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class MpesaCallbackController extends Controller
{
    // This handles actual payment (confirmation) from Paybill to your system
    public function handleC2BConfirmation(Request $request)
    {
        // Log for debug (optional)
        Log::info('C2B Confirmation Received:', $request->all());

        $data = $request->all();

        // Save the payment
        MpesaPayment::create([
            'transaction_type' => 'C2B Paybill',
            'trans_id' => $data['TransID'],
            'trans_time' => $data['TransTime'],
            'business_short_code' => $data['BusinessShortCode'],
            'bill_ref_number' => $data['BillRefNumber'], // typically order id or phone
            'invoice_number' => $data['InvoiceNumber'] ?? null,
            'trans_amount' => $data['TransAmount'],
            'msisdn' => $data['MSISDN'],
            'first_name' => $data['FirstName'] ?? '',
            'middle_name' => $data['MiddleName'] ?? '',
            'last_name' => $data['LastName'] ?? '',
            'order_id' => $data['BillRefNumber'] ?? null, // use this to match order
        ]);

        // ✅ Mark order paid if matched
        if (!empty($data['BillRefNumber'])) {
            Order::where('id', $data['BillRefNumber'])->update([
                'paid' => 1,
                'payment_method' => 'mpesa',
            ]);
        }

        return response()->json(["ResultCode" => 0, "ResultDesc" => "Confirmation received successfully"]);
    }

    // Optional: if Validation URL is set on Daraja
    public function handleC2BValidation(Request $request)
    {
        // You can add conditions to accept/reject a payment here
        return response()->json(["ResultCode" => 0, "ResultDesc" => "Validation passed"]);
    }
}
