<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Donation;
use App\Models\Setting;
use App\Models\User;
use App\Models\Blog;
use App\Services\NowPaymentsService;

class DonationController extends Controller
{
    protected NowPaymentsService $nowpayments;

    public function __construct(NowPaymentsService $nowpayments)
    {
        $this->nowpayments = $nowpayments;
    }

    public function index(Request $request)
    {
        $donationsEnabled = Setting::get('donations_enabled', '1') !== '0';
        $orderId = $request->query('order_id');
        $activeDonation = null;

        if ($orderId) {
            $activeDonation = Donation::where('order_id', $orderId)->first();
        }

        $success = $request->query('success') === '1' || $request->query('donation') === 'success';
        if ($activeDonation && in_array($activeDonation->status, ['confirmed', 'finished'])) {
            $success = true;
        }

        $canceled = $request->query('canceled') === '1' || $request->query('donation') === 'cancelled';
        $settings = Setting::pluck('value', 'key')->all();

        return view('pages.premium', compact('donationsEnabled', 'orderId', 'activeDonation', 'success', 'canceled', 'settings'));
    }

    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => 'Please sign in to donate or subscribe.', 'redirect' => route('login')], 401);
            }
            return redirect()->route('login')->with('error', 'Please log in to upgrade your account.');
        }

        $donationsEnabled = Setting::get('donations_enabled', '1') !== '0';
        if (!$donationsEnabled) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => 'Donations are currently paused by the administrator.'], 403);
            }
            return redirect()->back()->with('error', 'Donations are currently paused by the administrator.');
        }

        $plan = $request->input('plan', 'premium');
        $gateway = $request->input('gateway', 'nowpayments');
        $payCurrency = $request->input('pay_currency');
        $amount = ($plan === 'premium_plus') ? 30.00 : 15.00;

        /** @var User $user */
        $user = Auth::user();
        $userPlan = $user->plan;

        if ($userPlan === 'premium_plus') {
            $msg = 'You are already an active Premium+ ($30 VIP) member! You cannot order additional subscriptions while your 1-year membership is active.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($userPlan === 'premium' && $plan === 'premium') {
            $msg = 'You already have an active Premium ($15 PRO) membership! You cannot order Premium again, but you can upgrade to Premium+ ($30 VIP).';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $orderId = 'NINA-' . strtoupper(Str::random(10));

        // Create base donation record
        $donation = Donation::create([
            'user_id' => $user->id,
            'gateway' => $gateway,
            'order_id' => $orderId,
            'amount_usd' => $amount,
            'plan' => $plan,
            'status' => 'pending',
            'expires_at' => now()->addDays(365),
            'crypto_currency' => $payCurrency ? strtoupper($payCurrency) : null,
        ]);

        // Instant admin demo activation
        if ($gateway === 'instant' || $request->input('instant') === '1') {
            $donation->status = 'confirmed';
            $donation->crypto_currency = 'USDT';
            $donation->crypto_amount = $amount;
            $donation->save();

            $this->activateUserSubscription($user, $plan);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Subscription activated instantly! PRO badge and bonus points awarded.',
                    'redirect' => route('premium', ['success' => 1, 'order_id' => $orderId]),
                ]);
            }

            return redirect()->route('premium', ['success' => 1, 'order_id' => $orderId]);
        }

        // Bitcart Gateway Flow
        if ($gateway === 'bitcart') {
            $storeUrl = Setting::get('bitcart_url', 'https://bitcart.server5.click/store/01M2V1SFTBTD7TCN1HKKNKS2WK');
            $productId = Setting::get('bitcart_product_id', '01M0H6Q5G6GAQV5T0TJX27R8XD');
            
            $donation->gateway = 'bitcart';
            $donation->pay_address = $storeUrl;
            $donation->save();

            // Append order query parameters so Bitcart can track the subscriber
            $delimiter = str_contains($storeUrl, '?') ? '&' : '?';
            $targetUrl = $storeUrl . $delimiter . http_build_query([
                'order_id' => $orderId,
                'user' => $user->username,
                'email' => $user->email,
                'plan' => $plan,
                'product' => $productId,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'gateway' => 'bitcart',
                    'order_id' => $orderId,
                    'redirect' => $targetUrl,
                    'invoice_url' => $targetUrl,
                ]);
            }

            return redirect()->away($targetUrl);
        }

        // Official NOWPayments Hosted Invoice Flow
        try {
            $invoice = $this->nowpayments->createInvoice([
                'price_amount' => $amount,
                'price_currency' => 'usd',
                'pay_currency' => $payCurrency ?: null,
                'order_id' => $orderId,
                'order_description' => 'Nina.Sucks ' . ($plan === 'premium_plus' ? 'Premium+ ($30)' : 'Premium ($15)') . ' Subscription (@' . $user->username . ')',
                'ipn_callback_url' => url('/api/donations/nowpayments/ipn'),
                'success_url' => route('premium', ['success' => 1, 'order_id' => $orderId]),
                'cancel_url' => route('premium', ['canceled' => 1]),
            ]);

            $donation->payment_id = (string)($invoice['id'] ?? '');
            $donation->pay_address = $invoice['invoice_url'] ?? null;
            $donation->save();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'invoice_url' => $invoice['invoice_url'],
                    'order_id' => $orderId,
                    'redirect' => $invoice['invoice_url'],
                ]);
            }

            return redirect()->away($invoice['invoice_url']);
        } catch (\Throwable $e) {
            Log::error('NOWPayments checkout error: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => 'NOWPayments gateway error: ' . $e->getMessage()], 500);
            }

            return redirect()->route('premium')->with('error', 'Could not initialize NOWPayments gateway: ' . $e->getMessage());
        }
    }

    /**
     * Direct on-chain cryptocurrency payment API (generates unique deposit address & QR)
     */
    public function directPayment(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'error' => 'Please sign in to make a payment.'], 401);
        }

        $plan = $request->input('plan', 'premium');
        $payCurrency = strtolower($request->input('pay_currency', 'usdttrc20'));
        $amount = ($plan === 'premium_plus') ? 30.00 : 15.00;

        /** @var User $user */
        $user = Auth::user();

        // Enforce membership tier rules
        if ($user->plan === 'premium_plus') {
            return response()->json([
                'ok' => false,
                'error' => 'You are already subscribed to the highest tier (Premium+ VIP)! Your subscription is active for 1 year.'
            ], 422);
        }

        if ($user->plan === 'premium' && $plan === 'premium') {
            return response()->json([
                'ok' => false,
                'error' => 'You already have an active Premium ($15 PRO) membership! You cannot order Premium again, but you can upgrade to Premium+ ($30 VIP).'
            ], 422);
        }

        $orderId = 'NINA-DIR-' . strtoupper(Str::random(10));

        try {
            $payment = $this->nowpayments->createPayment([
                'price_amount' => $amount,
                'price_currency' => 'usd',
                'pay_currency' => $payCurrency,
                'order_id' => $orderId,
                'order_description' => 'Nina.Sucks ' . ($plan === 'premium_plus' ? 'Premium+' : 'Premium') . ' Direct Payment (@' . $user->username . ')',
                'ipn_callback_url' => url('/api/donations/nowpayments/ipn'),
                'customer_email' => $user->email,
            ]);

            $donation = Donation::create([
                'user_id' => $user->id,
                'gateway' => 'nowpayments_direct',
                'order_id' => $orderId,
                'payment_id' => (string)($payment['payment_id'] ?? ''),
                'amount_usd' => $amount,
                'plan' => $plan,
                'status' => $payment['payment_status'] ?? 'pending',
                'expires_at' => now()->addDays(365),
                'crypto_currency' => strtoupper($payment['pay_currency'] ?? $payCurrency),
                'crypto_amount' => $payment['pay_amount'] ?? null,
                'pay_address' => $payment['pay_address'] ?? null,
            ]);

            return response()->json([
                'ok' => true,
                'order_id' => $orderId,
                'payment_id' => $payment['payment_id'] ?? null,
                'pay_address' => $payment['pay_address'] ?? null,
                'pay_amount' => $payment['pay_amount'] ?? null,
                'pay_currency' => strtoupper($payment['pay_currency'] ?? $payCurrency),
                'price_amount' => $amount,
                'status' => $payment['payment_status'] ?? 'waiting',
            ]);
        } catch (\Throwable $e) {
            Log::error('Direct payment initialization error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Check order & payment status in real time
     */
    public function checkOrderStatus(string $orderId)
    {
        $donation = Donation::where('order_id', $orderId)->first();
        if (!$donation) {
            return response()->json(['ok' => false, 'error' => 'Order not found.'], 404);
        }

        // If payment is pending and we have a payment_id, query NOWPayments API directly
        if ($donation->payment_id && in_array($donation->status, ['pending', 'waiting', 'confirming'])) {
            try {
                $statusData = $this->nowpayments->getPaymentStatus($donation->payment_id);
                if ($statusData && !empty($statusData['payment_status'])) {
                    $newStatus = strtolower($statusData['payment_status']);
                    $donation->status = in_array($newStatus, ['confirmed', 'finished']) ? 'confirmed' : $newStatus;
                    if ($donation->status === 'confirmed' && !$donation->expires_at) {
                        $donation->expires_at = now()->addDays(365);
                    }

                    if (!empty($statusData['actually_paid']) || !empty($statusData['pay_amount'])) {
                        $donation->crypto_amount = $statusData['actually_paid'] ?? $statusData['pay_amount'];
                    }
                    if (!empty($statusData['pay_currency'])) {
                        $donation->crypto_currency = strtoupper($statusData['pay_currency']);
                    }
                    $donation->save();

                    if (in_array($newStatus, ['confirmed', 'finished'])) {
                        $user = $donation->user;
                        if ($user) {
                            $this->activateUserSubscription($user, $donation->plan);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Error querying live payment status: ' . $e->getMessage());
            }
        }

        return response()->json([
            'ok' => true,
            'order_id' => $donation->order_id,
            'status' => $donation->status,
            'amount_usd' => (float)$donation->amount_usd,
            'crypto_currency' => $donation->crypto_currency,
            'crypto_amount' => $donation->crypto_amount,
            'pay_address' => $donation->pay_address,
            'plan' => $donation->plan,
            'created_at' => $donation->created_at?->toIso8601String(),
        ]);
    }

    /**
     * NOWPayments IPN Webhook Notification Handler (Signed with HMAC-SHA512)
     */
    public function nowpaymentsIpn(Request $request)
    {
        $receivedHmac = $request->header('x-nowpayments-sig');
        $rawPayload = $request->getContent();

        Log::info('[NOWPayments IPN] Incoming notification: ' . $rawPayload);

        // Verify HMAC-SHA512 signature
        if (!$this->nowpayments->verifyIpnSignature($rawPayload, $receivedHmac)) {
            Log::warning('[NOWPayments IPN] Signature verification failed! Header: ' . $receivedHmac);
            return response('Invalid signature', 400);
        }

        $data = json_decode($rawPayload, true);
        $orderId = $data['order_id'] ?? null;
        $paymentStatus = strtolower($data['payment_status'] ?? '');
        $paymentId = $data['payment_id'] ?? null;
        $payCurrency = $data['pay_currency'] ?? null;
        $actuallyPaid = $data['actually_paid'] ?? $data['pay_amount'] ?? null;

        if (!$orderId) {
            return response('Missing order_id', 400);
        }

        $donation = Donation::where('order_id', $orderId)->first();
        if (!$donation) {
            Log::warning("[NOWPayments IPN] No donation found for order: {$orderId}");
            return response('Order not found', 404);
        }

        // Update donation details
        if ($paymentId) $donation->payment_id = (string)$paymentId;
        if ($payCurrency) $donation->crypto_currency = strtoupper($payCurrency);
        if ($actuallyPaid) $donation->crypto_amount = $actuallyPaid;

        if (in_array($paymentStatus, ['confirmed', 'finished'])) {
            $donation->status = 'confirmed';
            if (!$donation->expires_at) {
                $donation->expires_at = now()->addDays(365);
            }
            $donation->save();

            // Upgrade User
            $user = $donation->user;
            if ($user) {
                $this->activateUserSubscription($user, $donation->plan);
                Log::info("[NOWPayments IPN] Subscription activated for User #{$user->id} (@{$user->username}) via order {$orderId}");
            }
        } elseif (in_array($paymentStatus, ['failed', 'expired', 'refunded'])) {
            $donation->status = 'failed';
            $donation->save();
        } else {
            $donation->status = $paymentStatus;
            $donation->save();
        }

        return response('OK', 200);
    }

    /**
     * BitCart IPN Webhook
     */
    public function bitcartIpn(Request $request)
    {
        Log::info('Bitcart IPN Payload received: ' . json_encode($request->all()));

        $data = $request->all();
        $event = $data['event'] ?? '';
        $orderId = $data['order_id'] ?? ($data['custom_order_id'] ?? ($data['data']['order_id'] ?? ($data['data']['id'] ?? null)));
        $status = strtolower((string)($data['status'] ?? ($data['data']['status'] ?? ($data['event'] ?? ''))));
        $buyerEmail = $data['buyer_email'] ?? ($data['email'] ?? ($data['data']['buyer_email'] ?? ''));

        // Search donation by order ID
        $donation = null;
        if ($orderId) {
            $donation = Donation::where('order_id', $orderId)->first();
        }

        // Fallback by buyer email if order ID was not passed through
        if (!$donation && $buyerEmail) {
            $user = User::where('email', $buyerEmail)->first();
            if ($user) {
                $donation = Donation::where('user_id', $user->id)->where('status', '!=', 'confirmed')->latest()->first();
            }
        }

        $isComplete = in_array($status, ['complete', 'paid', 'confirmed', 'settled', 'invoice_complete', 'invoice_paid']) ||
                      str_contains($event, 'complete') || str_contains($event, 'paid');

        if ($donation && $isComplete) {
            if ($donation->status !== 'confirmed') {
                $donation->status = 'confirmed';
                $donation->crypto_currency = strtoupper((string)($data['currency'] ?? ($data['data']['currency'] ?? 'CRYPTO')));
                $donation->crypto_amount = floatval($data['amount'] ?? ($data['data']['price'] ?? $donation->amount_usd));
                if (!$donation->expires_at) {
                    $donation->expires_at = now()->addDays(365);
                }
                $donation->save();

                $user = $donation->user;
                if ($user) {
                    $this->activateUserSubscription($user, $donation->plan);
                    Log::info("Bitcart IPN: Successfully activated subscription for @{$user->username} (Order: {$donation->order_id})");
                }
            }
        } elseif ($donation) {
            $donation->status = $status ?: 'pending';
            $donation->save();
        }

        return response()->json(['ok' => true, 'message' => 'Bitcart webhook processed successfully']);
    }

    /**
     * Admin Test Simulation for Bitcart Webhook
     */
    public function simulateBitcartPayment(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['ok' => false, 'message' => 'Unauthorized.'], 403);
        }

        $user = auth()->user();
        $plan = $request->input('plan', 'premium_plus');
        $orderId = 'NINA-BC-TEST-' . strtoupper(Str::random(6));

        $donation = Donation::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'gateway' => 'bitcart',
            'amount_usd' => $plan === 'premium_plus' ? 30.00 : 15.00,
            'plan' => $plan,
            'status' => 'confirmed',
            'expires_at' => now()->addDays(365),
            'crypto_currency' => 'BTC',
            'crypto_amount' => 0.00035,
            'pay_address' => Setting::get('bitcart_url', 'https://bitcart.server5.click/store/01M2V1SFTBTD7TCN1HKKNKS2WK'),
            'payment_id' => 'bitcart_sim_' . time(),
        ]);

        $this->activateUserSubscription($user, $plan);

        return response()->json([
            'ok' => true,
            'message' => 'Simulated Bitcart payment confirmed for @' . $user->username . '! PRO badge and points granted.',
            'order_id' => $orderId,
        ]);
    }

    /**
     * Check NOWPayments API health for admin test button
     */
    public function apiStatus()
    {
        $status = $this->nowpayments->checkStatus();
        $key = $this->nowpayments->getApiKey();

        return response()->json(array_merge($status, [
            'api_key_masked' => $key ? (substr($key, 0, 7) . '...' . substr($key, -4)) : 'Not set',
            'ipn_url' => url('/api/donations/nowpayments/ipn'),
        ]));
    }

    /**
     * Get available currencies list
     */
    public function currencies()
    {
        $currencies = $this->nowpayments->getCurrencies();
        return response()->json(['ok' => true, 'currencies' => $currencies]);
    }

    /**
     * Helper to activate user subscription and award badges and points
     */
    protected function activateUserSubscription(User $user, string $plan): void
    {
        $bonusPoints = ($plan === 'premium_plus') ? 11000 : 5000;

        $user->badge_pro = true;
        if ($plan === 'premium_plus') {
            $user->badge_vip = true;
        }
        $user->increment('points', $bonusPoints);
        $user->save();

        // Ensure user's active confirmed donation has 1-year expiration set
        Donation::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'finished'])
            ->whereNull('expires_at')
            ->update(['expires_at' => now()->addDays(365)]);
    }
}
