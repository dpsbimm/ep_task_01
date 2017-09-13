<?php

namespace AppBundle\Exception\Payment;

use Throwable;

class InvalidCurrencyException extends InvalidPaymentInformationException
{
    /**
     * Constructor.
     *
     * @param string         $currency
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($currency, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid currency "%s"', $currency), $code, $previous);
    }
}
