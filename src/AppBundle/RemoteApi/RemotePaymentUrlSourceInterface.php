<?php

namespace AppBundle\RemoteApi;

interface RemotePaymentUrlSourceInterface
{
    /**
     * Getter: activeUrl.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set active URL by configuration key.
     *
     * @param string $urlKey
     */
    public function setUrl(string $urlKey): void;
}
