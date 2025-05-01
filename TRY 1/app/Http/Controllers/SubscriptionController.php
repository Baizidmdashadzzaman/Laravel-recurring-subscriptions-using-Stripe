<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Subscription;

class SubscriptionController extends Controller
{
    public function showForm()
    {
        // Pull your secret key from config/services.php → .env
        Stripe::setApiKey(config('services.stripe.secret'));

        // Create a SetupIntent so we can do 3DS right away
        $intent = SetupIntent::create();

        return view('subscribe', [
            'stripeKey'    => config('services.stripe.key'),
            'clientSecret' => $intent->client_secret,
        ]);
    }

    public function processSubscription(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // 1️⃣ Create the customer and attach the payment method
        $customer = Customer::create([
            'email'           => $request->email,
            'payment_method'  => $request->payment_method,
            'invoice_settings'=> [
                'default_payment_method' => $request->payment_method,
            ],
        ]);

        // 2️⃣ Create the subscription (uses your price ID)
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items'    => [
                ['price' => 'price_1RJScrP2kqOTkjJTBj6UAtic'],  // ← replace with your actual Price ID
            ],
            // expand the first invoice’s PI so we can handle any on-first-payment 3DS
            'expand'   => ['latest_invoice.payment_intent'],
        ]);

        $pi = $subscription->latest_invoice->payment_intent;

        // 3️⃣ If the first invoice needs an extra 3DS step, tell the frontend
        if ($pi && $pi->status === 'requires_action') {
            return response()->json([
                'requiresAction'       => true,
                'paymentIntentSecret'  => $pi->client_secret,
            ]);
        }

        // 4️⃣ Otherwise, we’re all set!
        return response()->json([
            'requiresAction' => false,
            'message'        => 'Subscription is active!',
        ]);
    }
}
