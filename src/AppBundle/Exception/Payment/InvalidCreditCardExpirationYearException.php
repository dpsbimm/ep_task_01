<?php

namespace AppBundle\Exception\Payment;

use Throwable;

class InvalidCreditCardExpirationYearException extends InvalidPaymentInformationException
{
    /**
     * Constructor.
     *
     * @param string         $expirationYear
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($expirationYear, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid credit card expiration year "%s"', $expirationYear), $code, $previous);
    }
}
