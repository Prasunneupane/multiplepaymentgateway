<?php

// app/Services/PaymentGatewayFactory.php (New Factory for dynamic selection)

namespace App\Services;

use App\Contracts\PaymentGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public function create(string $gateway): PaymentGateway
    {
        return match ($gateway) {
            'fonepay' => app(FonePayGateway::class),
            'esewa' => app(EsewaGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}"),
        };
    }
}