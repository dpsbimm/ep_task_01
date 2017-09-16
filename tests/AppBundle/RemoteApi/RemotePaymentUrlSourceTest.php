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
        $this->source = new RemotePaymentUrlSource([
            'production'                  => 'http://localhost/payment/api/production',
            'dev.invalidjs'               => 'http://localhost/payment/api/dev/invalid-js',
            'dev.ok'                      => 'http://localhost/payment/api/dev/ok',
            'dev.decline.amount_exceeded' => 'http://localhost/payment/api/dev/decline/amount-exceeded',
        ]);
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
        $this->assertSame('http://localhost/payment/api/production', $this->source->getUrl());
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
                'http://localhost/payment/api/production',
            ],
            'dev.invalidjs' => [
                'dev.invalidjs',
                'http://localhost/payment/api/dev/invalid-js',
            ],
            'dev.ok' => [
                'dev.ok',
                'http://localhost/payment/api/dev/ok',
            ],
            'dev.decline.amount_exceeded' => [
                'dev.decline.amount_exceeded',
                'http://localhost/payment/api/dev/decline/amount-exceeded',
            ],
        ];
    }

    public function testSetUrlExceptionInvalidKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->source->setUrl('does not exist');
    }
}
