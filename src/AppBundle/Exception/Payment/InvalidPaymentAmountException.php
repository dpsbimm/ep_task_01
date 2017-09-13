<?php

namespace AppBundle\Exception\Payment;

use Throwable;

class InvalidPaymentAmountException extends InvalidPaymentInformationException
{
    /**
     * Constructor.
     *
     * @param int            $amount
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($amount, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid payment amount "%s"', $amount), $code, $previous);
    }
}
