<?php

// app/Http/Controllers/PaymentController.php (Updated for dynamic gateway selection)

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use App\Services\PaymentGatewayFactory;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentGatewayFactory $factory;

    public function __construct(PaymentGatewayFactory $factory)
    {
        $this->factory = $factory;
    }

    public function index()
    {
        return view('welcome');
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:fonepay,esewa',
            'amount' => 'required|numeric|min:1',
        ]);

        $method = $request->payment_method;
        $amount = $request->amount;

        $returnUrl = route('payment.verify', ['gateway' => $method]);
        $orderData = ['amount' => $amount, 'return_url' => $returnUrl];

        // In real app: Create order, generate ref, store ref with amount, pass ref in orderData

        $gateway = $this->factory->create($method);
        $paymentData = $gateway->initiatePayment($orderData);

        // Store ref if needed, e.g., for esewa: Order::create(['ref' => $paymentData['transaction_uuid'], 'amount' => $amount]);

        return view('payment_form', compact('paymentData'));
    }

    public function verify(Request $request, string $gateway)
    {
        // In real app: Get ref from request (PRN for fonepay, decode data for esewa), fetch order by ref, get amount
        $orderData = ['amount' => 10.00]; // Demo

        $gatewayInstance = $this->factory->create($gateway);
        $isVerified = $gatewayInstance->verifyPayment($request, $orderData);

        if ($isVerified) {
            // Update order status
            return 'Payment verification completed';
        }

        return 'Payment verification failed';
    }

    public function failure()
    {
        return 'Payment Failed';
    }
}