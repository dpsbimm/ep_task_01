<?php

namespace AppBundle\Exception\Payment;

use Throwable;

class EmptyFirstNameException extends InvalidPaymentInformationException
{
    /**
     * Constructor.
     *
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('First name is empty', $code, $previous);
    }
}
