<?php

namespace Tests\AppBundle\RemoteApi;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\RemoteApi\RemotePaymentUrlSource;
use PHPUnit\Framework\TestCase;

class RemotePaymentUrlSourceTest extends TestCase
{
    /**
     * @var RemotePaymentUrlSource
     */
    private $source;

    /**
     * PHPUnit: setUp.
     */
    public function setUp(): void
    {
        $this->source = new RemotePaymentUrlSource();
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown(): void
    {
        $this->source = null;
    }

    public function testConstructorSuccess(): void
    {
        $this->assertSame('https://pastebin.com/raw/288PX9T6', $this->source->getUrl());
    }

    /**
     * @param string $configKey
     * @param string $expectedUrl
     *
     * @dataProvider provideGetSetUrlData
     */
    public function testGetSetUrlSuccess(string $configKey, string $expectedUrl): void
    {
        $this->source->setUrl($configKey);

        $this->assertSame($expectedUrl, $this->source->getUrl());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideGetSetUrlData(): array
    {
        return [
            'production' => [
                'production',
                'https://pastebin.com/raw/288PX9T6',
            ],
            'dev.invalidjs' => [
                'dev.invalidjs',
                'https://pastebin.com/raw/LrCS4j0c',
            ],
            'dev.ok' => [
                'dev.ok',
                'https://pastebin.com/raw/288PX9T6',
            ],
            'dev.decline.amount_exceeded' => [
                'dev.decline.amount_exceeded',
                'https://pastebin.com/raw/1fpTv46q',
            ],
        ];
    }

    public function testSetUrlExceptionInvalidKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->source->setUrl('does not exist');
    }
}
