<?php

namespace App\Http\Controllers;

class Stripe extends Controller
{
    //
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(config('stripe.api_key.secret'));
    }

    public function pay()
    {

        for ($i = 0; $i < 3; $i++) {
            $product[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'T-shirt',
                        'description' => 'T-shirt to T-shirt',

                    ],
                    'unit_amount' => ($i + 1) * 10 * 100,
                ],
                'quantity' => 2,
            ];
        }
        $session = $this->stripe->checkout->sessions->create([
            'line_items' => $product,
            'mode' => 'payment',
            'success_url' => 'http://127.0.0.1:8000/success',
            'cancel_url' => 'http://127.0.0.1:8000/cancel',
        ]);

        return redirect($session->url);
    }
}
