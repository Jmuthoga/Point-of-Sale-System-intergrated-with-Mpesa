<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MpesaPayment;
use Illuminate\Http\Request;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    /**
     * Trigger STK Push
     */
    public function stkPush(Request $request, MpesaService $mpesa)
    {
        $request->validate([
            'phone' => 'required|regex:/^2547\d{8}$/',
            'amount' => 'required|numeric|min:1',
            'order_id' => 'required|exists:orders,id',
        ]);

        $response = $mpesa->stkPush(
            $request->amount,
            $request->phone,
            $request->order_id
        );

        if ($response['ResponseCode'] == '0') {
            return response()->json([
                'success' => true,
                'message' => 'STK Push sent. Complete payment on your phone.',
                'checkout_request_id' => $response['CheckoutRequestID'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['errorMessage'] ?? 'STK Push failed.',
        ]);
    }

    /**
     * Handle STK Push Callback from Safaricom
     */
    public function handleStkCallback(Request $request)
    {
        $data = $request->all()['Body']['stkCallback'];
        Log::info('STK Callback Received', $data);

        if ($data['ResultCode'] == 0) {
            $metadata = collect($data['CallbackMetadata']['Item'] ?? [])->mapWithKeys(function ($item) {
                return [$item['Name'] => $item['Value'] ?? null];
            });

            $orderId = $metadata['AccountReference'] ?? null;
            $receipt = $metadata['MpesaReceiptNumber'] ?? null;

            // Prevent duplicates
            $existing = MpesaPayment::where('trans_id', $receipt)->first();
            if (!$existing && $orderId) {
                MpesaPayment::create([
                    'transaction_type' => 'STK Push',
                    'trans_id' => $receipt,
                    'trans_time' => now(),
                    'business_short_code' => config('mpesa.shortcode'),
                    'bill_ref_number' => $orderId,
                    'invoice_number' => null,
                    'trans_amount' => $metadata['Amount'],
                    'msisdn' => $metadata['PhoneNumber'],
                    'first_name' => '',
                    'middle_name' => '',
                    'last_name' => '',
                    'order_id' => $orderId,
                ]);

                Order::where('id', $orderId)->update([
                    'paid' => 1,
                    'payment_method' => 'mpesa',
                ]);
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'STK Callback Accepted']);
    }

    /**
     * Handle C2B Paybill direct deposit (manual payment)
     */
    public function handleC2BCallback(Request $request)
    {
        $data = $request->all();
        Log::info('C2B Callback Received', $data);

        $trans = $data['TransID'] ?? null;
        $orderId = $data['BillRefNumber'] ?? null;

        $existing = MpesaPayment::where('trans_id', $trans)->first();

        if (!$existing && $trans && $orderId) {
            MpesaPayment::create([
                'transaction_type' => 'C2B Paybill',
                'trans_id' => $trans,
                'trans_time' => $data['TransTime'] ?? now(),
                'business_short_code' => config('mpesa.shortcode'),
                'bill_ref_number' => $orderId,
                'invoice_number' => null,
                'trans_amount' => $data['TransAmount'] ?? 0,
                'msisdn' => $data['MSISDN'] ?? '',
                'first_name' => $data['FirstName'] ?? '',
                'middle_name' => $data['MiddleName'] ?? '',
                'last_name' => $data['LastName'] ?? '',
                'order_id' => $orderId,
            ]);

            Order::where('id', $orderId)->update([
                'paid' => 1,
                'payment_method' => 'mpesa',
            ]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'C2B Callback Accepted']);
    }
}
