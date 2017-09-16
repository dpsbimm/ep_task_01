<?php

namespace AppBundle\RemoteApi;

use AppBundle\Exception\InvalidArgumentException;

class RemotePaymentUrlSource implements RemotePaymentUrlSourceInterface
{
    const DEFAULT_CONFIGURATION_KEY = 'production';

    /**
     * @var array
     */
    private $configs = [
        'production'                  => 'https://pastebin.com/raw/288PX9T6',
        'dev.invalidjs'               => 'https://pastebin.com/raw/LrCS4j0c',
        'dev.ok'                      => 'https://pastebin.com/raw/288PX9T6',
        'dev.decline.amount_exceeded' => 'https://pastebin.com/raw/1fpTv46q',
    ];

    /**
     * @var string
     */
    private $activeUrl = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setUrl(self::DEFAULT_CONFIGURATION_KEY);
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
     * Set URL by configuration key.
     *
     * @param string $configKey
     *
     * @throws InvalidArgumentException
     */
    public function setUrl(string $configKey): void
    {
        if (!array_key_exists($configKey, $this->configs)) {
            throw new InvalidArgumentException('Invalid configuration key');
        }

        $this->activeUrl = $this->configs[$configKey];
    }
}
