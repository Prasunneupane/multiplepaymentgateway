<?php

// app/Contracts/PaymentGateway.php

namespace App\Contracts;

use Illuminate\Http\Request;

interface PaymentGateway
{
    /**
     * Prepare data for initiating payment.
     *
     * @param array $orderData
     * @return array
     */
    public function initiatePayment(array $orderData): array;

    /**
     * Verify the payment response.
     *
     * @param Request $request
     * @param array $orderData
     * @return bool
     */
    public function verifyPayment(Request $request, array $orderData): bool;
}