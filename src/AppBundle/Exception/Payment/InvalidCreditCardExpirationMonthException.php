<?php

namespace AppBundle\Exception\Payment;

use Throwable;

class InvalidCreditCardExpirationMonthException extends InvalidPaymentInformationException
{
    /**
     * Constructor.
     *
     * @param string         $expirationMonth
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($expirationMonth, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid credit card expiration month "%s"', $expirationMonth), $code, $previous);
    }
}
