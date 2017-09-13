<?php

namespace AppBundle\Exception\Payment;

use Throwable;

class EmptyLastNameException extends InvalidPaymentInformationException
{
    /**
     * Constructor.
     *
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Last name is empty', $code, $previous);
    }
}
