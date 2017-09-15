<?php

namespace AppBundle\RemoteApi;

use AppBundle\Payment\Payment;

interface RemotePaymentProcessorInterface
{
    /**
     * Send payment request.
     *
     * @param Payment $payment
     *
     * @return array
     */
    public function sendPaymentRequest(Payment $payment): array;
}
