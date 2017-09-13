<?php

namespace AppBundle\Exception\Payment;

use Throwable;

class InvalidCreditCardCvvException extends InvalidPaymentInformationException
{
    /**
     * Constructor.
     *
     * @param string         $cvv
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($cvv, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid credit card CVV "%s"', $cvv), $code, $previous);
    }
}
