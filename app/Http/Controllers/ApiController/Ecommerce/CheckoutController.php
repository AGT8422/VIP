<?php

namespace App\Http\Controllers\ApiController\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
class CheckoutController extends Controller
{
    // Enter Your Stripe Secret
    public function checkout() {
        // Enter Your Stripe Secret
        // \Stripe\Stripe::setApiKey('sk_test_51NYUJmIasJEHL6yeVNbsHPBVnNlK0tRyA1cEM6ebYW3CCln5c8mHuKnKHIRxPZLODHVqa7QR2qPwwPhf042Vm4aW00TnBVxkgK');
        // $amount         = 100;
        // $amount1        = 66;
        // $amount        *= 66;
        // $amount         = (int) $amount;
        // $payment_intent = \Stripe\PaymentIntent::create([
        //                 'description'          => 'Stripe Test Payment',
        //                 'amount'               => $amount,
        //                 'currency'             => 'AED',
        //                 'description'          => 'Payment From Izo E-commerce' ,
        //                 'payment_method_types' => ['card'],
        // ]);
        // $intent         = $payment_intent->client_secret;
        // return view('checkout.credit-card',compact('intent','amount1'));
        // <?php
        // require_once '../vendor/autoload.php';
        // require_once '../secrets.php';
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $YOUR_DOMAIN      = config('https://izo-ecommerce-frontend.vercel.app/');
            $checkout_session = Session::create([
                'line_items' => [[
                    'price' => "{{PRICE_ID}}",
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success.html',
                'cancel_url'  => $YOUR_DOMAIN . '/cancel.html',
            ]);
            return redirect()->away($checkout_session->url);
        } catch (\Throwable $th) {
            return response([
                "status"  => 400,
                "message" => __('Failed Action')
            ],400);
        }
       
    }
    // Enter Your Stripe Secret
    public function afterPayment() {
        echo "Payment Received, Thanks you for using our service";
        
    }
}
