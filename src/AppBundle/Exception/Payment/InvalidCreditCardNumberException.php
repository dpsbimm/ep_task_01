<?php

namespace AppBundle\Exception\Payment;

use Throwable;

class InvalidCreditCardNumberException extends InvalidPaymentInformationException
{
    /**
     * Constructor.
     *
     * @param string         $creditCardNumber
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($creditCardNumber, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid credit card number "%s"', $creditCardNumber), $code, $previous);
    }
}
