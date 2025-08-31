<?php

// app/Services/EsewaGateway.php (New class for eSewa)

namespace App\Services;

use App\Contracts\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EsewaGateway implements PaymentGateway
{
    protected string $merchantCode;
    protected string $secretKey;
    protected string $baseUrl;
    protected string $failureUrl;

    public function __construct()
    {
        $this->merchantCode = config('payments.gateways.esewa.merchant_code');
        $this->secretKey = config('payments.gateways.esewa.secret_key');
        $this->baseUrl = config('payments.gateways.esewa.base_url');
        $this->failureUrl = config('payments.gateways.esewa.failure_url', route('payment.failure'));
    }

    public function initiatePayment(array $orderData): array
    {
        $amount = $orderData['amount'] ?? 10.00;
        $returnUrl = $orderData['return_url'] ?? route('payment.verify', ['gateway' => 'esewa']);
        $transactionUuid = Str::uuid()->toString();

        $totalAmount = $amount;
        $taxAmount = 0;
        $serviceCharge = 0;
        $deliveryCharge = 0;

        $message = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$this->merchantCode}";
        $signature = base64_encode(hash_hmac('sha256', $message, $this->secretKey, true));

        return [
            'payment_url' => $this->baseUrl . '/api/epay/main/v2/form',
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'transaction_uuid' => $transactionUuid,
            'product_code' => $this->merchantCode,
            'product_service_charge' => $serviceCharge,
            'product_delivery_charge' => $deliveryCharge,
            'success_url' => $returnUrl,
            'failure_url' => $this->failureUrl,
            'signed_field_names' => 'total_amount,transaction_uuid,product_code',
            'signature' => $signature,
        ];
    }

    public function verifyPayment(Request $request, array $orderData): bool
    {
        $encodedData = $request->query('data');
        if (!$encodedData) {
            return false;
        }

        $result = json_decode(base64_decode($encodedData), true);
        if (!$result || !isset($result['transaction_uuid'], $result['product_code'], $result['total_amount'])) {
            return false;
        }

        // In a real app, fetch original amount using transaction_uuid from DB
        $totalAmount = str_replace(',', '', $result['total_amount']); // Use original if possible

        $response = Http::get($this->baseUrl . '/api/epay/transaction/status/', [
            'product_code' => $result['product_code'],
            'total_amount' => $totalAmount,
            'transaction_uuid' => $result['transaction_uuid'],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return ($data['status'] ?? '') === 'COMPLETE';
        }

        return false;
    }
}