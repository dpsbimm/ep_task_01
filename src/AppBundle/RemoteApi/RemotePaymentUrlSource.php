<?php

namespace AppBundle\RemoteApi;

use AppBundle\Exception\InvalidArgumentException;

class RemotePaymentUrlSource implements RemotePaymentUrlSourceInterface
{
    const DEFAULT_URL_KEY = 'production';

    /**
     * @var array
     */
    private $urlMap = [
        self::DEFAULT_URL_KEY => '',
    ];

    /**
     * @var string
     */
    private $activeUrl = '';

    /**
     * Constructor.
     *
     * @param array $urls
     */
    public function __construct(array $urls)
    {
        $this->urlMap = $urls;

        $this->setUrl(self::DEFAULT_URL_KEY);
    }

    /**
     * Getter: url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->activeUrl;
    }

    /**
     * Set URL by key.
     *
     * @param string $urlKey
     *
     * @throws InvalidArgumentException
     */
    public function setUrl(string $urlKey): void
    {
        if (!array_key_exists($urlKey, $this->urlMap)) {
            throw new InvalidArgumentException('Invalid URL key');
        }

        $this->activeUrl = $this->urlMap[$urlKey];
    }
}
