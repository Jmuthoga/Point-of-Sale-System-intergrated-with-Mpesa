<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RegisterMpesaUrls extends Command
{
    protected $signature = 'mpesa:register-urls';
    protected $description = 'Register MPESA C2B URLs to Safaricom API';

    public function handle()
    {
        $shortcode = env('MPESA_SHORTCODE');
        $consumerKey = env('MPESA_CONSUMER_KEY');
        $consumerSecret = env('MPESA_CONSUMER_SECRET');
        $validationUrl = env('MPESA_CALLBACK_URL'); // Or a separate one
        $confirmationUrl = env('MPESA_CALLBACK_URL');

        // Step 1: Get Safaricom Access Token
        $tokenResponse = Http::withBasicAuth($consumerKey, $consumerSecret)
            ->get('https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        if ($tokenResponse->failed()) {
            $this->error('Could not get access token: ' . $tokenResponse->body());
            return 1;
        }

        $accessToken = $tokenResponse['access_token'];
        $this->info("Access Token: " . $accessToken);

        // Step 2: Send Registration Request to Safaricom
        $registerResponse = Http::withToken($accessToken)
            ->post('https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl', [
                'ShortCode' => $shortcode,
                'ResponseType' => 'Completed',
                'ConfirmationURL' => $confirmationUrl,
                'ValidationURL' => $validationUrl,
            ]);

        if ($registerResponse->successful()) {
            $this->info('🎉 MPESA URLs registered successfully!');
            $this->line($registerResponse->body());
        } else {
            $this->error('❌ Registration failed:');
            $this->line($registerResponse->body());
        }

        return 0;
    }
}
