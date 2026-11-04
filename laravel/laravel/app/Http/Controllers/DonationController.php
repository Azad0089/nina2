<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Donation;
use App\Models\Setting;
use App\Models\User;

class DonationController extends Controller
{
    public function index()
    {
        $donationsEnabled = Setting::get('donations_enabled', '1') !== '0';
        return view('pages.premium', compact('donationsEnabled'));
    }

    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to upgrade your account.');
        }

        $donationsEnabled = Setting::get('donations_enabled', '1') !== '0';
        if (!$donationsEnabled) {
            return redirect()->back()->with('error', 'Donations are currently paused by the administrator.');
        }

        $plan = $request->input('plan', 'premium');
        $gateway = $request->input('gateway', 'nowpayments');
        $amount = $plan === 'premium_plus' ? 30.00 : 15.00;

        $user = Auth::user();
        $orderId = 'NINA-' . strtoupper(Str::random(10));

        $donation = Donation::create([
            'user_id' => $user->id,
            'gateway' => $gateway,
            'order_id' => $orderId,
            'amount_usd' => $amount,
            'plan' => $plan,
            'status' => 'pending',
        ]);

        if ($gateway === 'nowpayments') {
            $apiKey = Setting::get('nowpayments_api_key', '4DMYGP5-7V04ZZJ-G3JTHJH-XGNZHD5');
            try {
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.nowpayments.io/v1/invoice', [
                    'price_amount' => $amount,
                    'price_currency' => 'usd',
                    'order_id' => $orderId,
                    'order_description' => 'Nina.Sucks ' . ucfirst(str_replace('_', ' ', $plan)) . ' Donation',
                    'ipn_callback_url' => url('/api/donations/nowpayments/ipn'),
                    'success_url' => url('/dashboard?donation=success'),
                    'cancel_url' => url('/premium?donation=cancelled'),
                ]);

                $data = $response->json();
                if (isset($data['invoice_url'])) {
                    $donation->payment_id = $data['id'] ?? null;
                    $donation->save();
                    return redirect()->away($data['invoice_url']);
                }
            } catch (\Throwable $e) {}
        }

        if ($gateway === 'bitcart') {
            $storeUrl = Setting::get('bitcart_url', 'https://bitcart.server5.click/store/01M2V1SFTBTD7TCN1HKKNKS2WK');
            return redirect()->away($storeUrl);
        }

        // Fallback info if live API is temporarily unreachable in dev
        return redirect()->route('dashboard')->with('success', 'Order #' . $orderId . ' initialized. Contact support for direct crypto address.');
    }

    public function nowpaymentsIpn(Request $request)
    {
        $ipnSecret = Setting::get('nowpayments_ipn_secret', 'mIV4c5kQrKwYN4PEGRV4L5MM8UQUhAEz');
        $receivedHmac = $request->header('x-nowpayments-sig');
        $payload = $request->getContent();

        if ($receivedHmac) {
            $expectedHmac = hash_hmac('sha512', $payload, $ipnSecret);
            if ($receivedHmac !== $expectedHmac) {
                return response('Invalid signature', 400);
            }
        }

        $data = json_decode($payload, true);
        $orderId = $data['order_id'] ?? null;
        $paymentStatus = $data['payment_status'] ?? null;

        if ($orderId && ($paymentStatus === 'finished' || $paymentStatus === 'confirmed')) {
            $donation = Donation::where('order_id', $orderId)->first();
            if ($donation && $donation->status !== 'confirmed') {
                $donation->status = 'confirmed';
                $donation->payment_id = $data['payment_id'] ?? $donation->payment_id;
                $donation->crypto_currency = $data['pay_currency'] ?? null;
                $donation->crypto_amount = $data['pay_amount'] ?? null;
                $donation->save();

                // Award user badges & reward points
                $user = $donation->user;
                if ($user) {
                    $user->badge_pro = true;
                    $bonusPoints = ($donation->plan === 'premium_plus') ? 11000 : 5000;
                    $user->increment('points', $bonusPoints);
                }
            }
        }

        return response('OK', 200);
    }

    public function bitcartIpn(Request $request)
    {
        $data = $request->all();
        $orderId = $data['order_id'] ?? null;
        $status = $data['status'] ?? null;

        if ($orderId && in_array($status, ['complete', 'paid'])) {
            $donation = Donation::where('order_id', $orderId)->first();
            if ($donation && $donation->status !== 'confirmed') {
                $donation->status = 'confirmed';
                $donation->save();

                $user = $donation->user;
                if ($user) {
                    $user->badge_pro = true;
                    $bonusPoints = ($donation->plan === 'premium_plus') ? 11000 : 5000;
                    $user->increment('points', $bonusPoints);
                }
            }
        }

        return response('OK', 200);
    }
}
