<?php

// app/Services/FonePayGateway.php (Updated to accept return_url in orderData)

namespace App\Services;

use App\Contracts\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FonePayGateway implements PaymentGateway
{
    protected string $merchantId;
    protected string $sharedSecretKey;
    protected string $paymentUrl;
    protected string $verifyUrl;

    public function __construct()
    {
        $config = config('payments.gateways.fonepay');
        $this->merchantId = $config['merchant_id'];
        $this->sharedSecretKey = $config['shared_secret_key'];
        $this->paymentUrl = $config['payment_url'];
        $this->verifyUrl = $config['verify_url'];
    }

    public function initiatePayment(array $orderData): array
    {
        $amount = $orderData['amount'] ?? 10.00;
        $returnUrl = $orderData['return_url'] ?? route('payment.verify', ['gateway' => 'fonepay']);
        $prn = $this->generateUniquePrn();
        $date = date('d/m/Y');

        $xml = "MD=P,PID={$this->merchantId},PRN={$prn},AMT={$amount},CRN=NPR,DT={$date},R1=test,R2=test,RU={$returnUrl}";
        $dv = base64_encode(hash_hmac('sha512', $xml, $this->sharedSecretKey, true));

        return [
            'payment_url' => $this->paymentUrl,
            'PID' => $this->merchantId,
            'MD' => 'P',
            'PRN' => $prn,
            'AMT' => $amount,
            'CRN' => 'NPR',
            'DT' => $date,
            'R1' => 'test',
            'R2' => 'test',
            'RU' => $returnUrl,
            'DV' => $dv,
        ];
    }

    public function verifyPayment(Request $request, array $orderData): bool
    {
        $prn = $request->input('PRN');
        $bid = $request->input('BID') ?? '';
        $uid = $request->input('UID');
        $amount = $orderData['amount'] ?? 10.00; // Original amount

        $xml = "PID={$this->merchantId},BID={$bid},AMT={$amount},PRN={$prn},UID={$uid}";
        $dv = base64_encode(hash_hmac('sha512', $xml, $this->sharedSecretKey, true));

        $response = Http::get($this->verifyUrl, [
            'PID' => $this->merchantId,
            'BID' => $bid,
            'AMT' => $amount,
            'PRN' => $prn,
            'UID' => $uid,
            'DV' => $dv,
        ]);

        $xmlResponse = simplexml_load_string($response->body());

        return (string) $xmlResponse->success === 'true';
    }

    protected function generateUniquePrn(): string
    {
        return uniqid('order_');
    }
}