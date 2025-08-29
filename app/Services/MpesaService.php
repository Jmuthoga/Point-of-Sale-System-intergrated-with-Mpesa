<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    public function getAccessToken()
    {
        $baseUrl = config('mpesa.base_url');

        $response = Http::withBasicAuth(
            config('mpesa.consumer_key'),
            config('mpesa.consumer_secret')
        )->get("$baseUrl/oauth/v1/generate?grant_type=client_credentials");

        if (!$response->successful()) {
            logger()->error('Access token error', ['response' => $response->body()]);
            throw new \Exception('Failed to get access token');
        }

        return $response->json()['access_token'];
    }

public function stkPush($amount, $phone, $account)
{
    $accessToken = $this->getAccessToken();

    $timestamp = now()->format('YmdHis');
    $password = base64_encode(config('mpesa.shortcode') . config('mpesa.passkey') . $timestamp);

    $response = Http::withToken($accessToken)->post(config('mpesa.base_url') . '/mpesa/stkpush/v1/processrequest', [
        "BusinessShortCode" => config('mpesa.shortcode'),
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => config('mpesa.shortcode'),
        "PhoneNumber" => $phone,
        "CallBackURL" => config('mpesa.stk_callback_url'),
        "AccountReference" => $account,
        "TransactionDesc" => "POS Payment"
    ]);

    if (!$response->successful()) {
        logger()->error('STK Push failed', ['response' => $response->body()]);
        return [
            'ResponseCode' => '1',
            'errorMessage' => 'STK Push request failed',
        ];
    }

    return $response->json();
}

}
