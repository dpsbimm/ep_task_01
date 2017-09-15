<?php

namespace AppBundle\Exception\RemoteApi;

use Throwable;

class InvalidResponseException extends RemoteApiException
{
    /**
     * Constructor.
     *
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid response'), $code, $previous);
    }
}
