<?php

namespace AppBundle\Exception\RemoteApi;

use Throwable;

class UnexpectedResponseStatusException extends RemoteApiException
{
    /**
     * Constructor.
     *
     * @param int            $statusCode
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($statusCode, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Unexpected response status "%s"', $statusCode), $code, $previous);
    }
}
