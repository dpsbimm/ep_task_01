<?php

namespace Tests\AppBundle\RemoteApi;

use AppBundle\Exception\RemoteApi\InvalidResponseException;
use AppBundle\Exception\RemoteApi\UnexpectedResponseStatusException;
use AppBundle\Payment\Payment;
use AppBundle\RemoteApi\RemotePaymentProcessor;
use AppBundle\RemoteApi\RemotePaymentUrlSource;
use AppBundle\RemoteApi\RemotePaymentUrlSourceInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RemotePaymentProcessorTest extends TestCase
{
    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var RemotePaymentProcessor
     */
    private $processor;

    /**
     * @var RemotePaymentUrlSourceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlSource;

    /**
     * PHPUnit: setUp.
     */
    public function setUp(): void
    {
        $this->client = $this->createClientInterfaceMock();
        $this->urlSource = $this->createRemotePaymentUrlSourceMock();

        $this->processor = new RemotePaymentProcessor($this->client, $this->urlSource);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown(): void
    {
        $this->processor = null;
        $this->urlSource = null;
        $this->client = null;
    }

    public function testSendPaymentRequestExceptionUnexpectedResponseStatus(): void
    {
        $this->expectException(UnexpectedResponseStatusException::class);

        $payment = $this->generateExamplePayment();
        $apiUrl = 'http://localhost/payment/api/';

        $prepResponse = new Response(500);

        $this->setUpClientRequest($payment, $apiUrl, $prepResponse);
        $this->setUpUrlSourceGetUrl($apiUrl);

        $this->processor->sendPaymentRequest($payment);
    }

    /**
     * @param string $responseBody
     *
     * @dataProvider provideInvalidResponseBodyData
     */
    public function testSendPaymentRequestExceptionInvalidResponse(string $responseBody): void
    {
        $this->expectException(InvalidResponseException::class);

        $payment = $this->generateExamplePayment();
        $apiUrl = 'http://localhost/payment/api/';

        $prepResponse = new Response(200, [], $responseBody);

        $this->setUpClientRequest($payment, $apiUrl, $prepResponse);
        $this->setUpUrlSourceGetUrl($apiUrl);

        $this->processor->sendPaymentRequest($payment);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidResponseBodyData(): array
    {
        return [
            'not JSON' => [
                'not JSON',
            ],
            'valid JSON but missing key "result"' => [
                '{"resultCode":1,"id":123}',
            ],
            'valid JSON but missing key "resultCode"' => [
                '{"result":"OK","id":123}',
            ],
        ];
    }

    /**
     * @param array $responseData
     *
     * @dataProvider provideValidResponseBodyData
     */
    public function testSendPaymentRequestSuccess(array $responseData): void
    {
        $payment = $this->generateExamplePayment();
        $apiUrl = 'http://localhost/payment/api/';

        $prepResponse = new Response(200, [], json_encode($responseData));

        $this->setUpClientRequest($payment, $apiUrl, $prepResponse);
        $this->setUpUrlSourceGetUrl($apiUrl);

        $response = $this->processor->sendPaymentRequest($payment);

        $this->assertSame($responseData, $response);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideValidResponseBodyData(): array
    {
        return [
            'response = OK' => [
                [
                    'result'     => 'OK',
                    'resultCode' => 1,
                    'id'         => 123,
                ],
            ],
            'response = DECLINE' => [
                [
                    'result'        => 'DECLINE',
                    'resultCode'    => 555,
                    'resultMessage' => 'Amount exceed',
                ],
            ],
        ];
    }

    /**
     * Create mock for ClientInterface.
     *
     * @return ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createClientInterfaceMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ClientInterface::class)->getMock();
    }

    /**
     * Create mock for RemotePaymentUrlSource.
     *
     * @return RemotePaymentUrlSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRemotePaymentUrlSourceMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(RemotePaymentUrlSource::class)->getMock();
    }

    /**
     * Generate example payment.
     *
     * @return Payment
     */
    private function generateExamplePayment(): Payment
    {
        return new Payment(
            12345,
            '123',
            10,
            (date('Y') + 1),
            '1234567890123456',
            'EUR',
            'Foo',
            'Bauer'
        );
    }

    /**
     * Set up client: request.
     *
     * @param Payment  $payment
     * @param string   $apiUrl
     * @param Response $response
     */
    private function setUpClientRequest(Payment $payment, string $apiUrl, Response $response): void
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with(
                $this->identicalTo('GET'),
                $this->identicalTo($apiUrl),
                $this->identicalTo([
                    'json' => [
                        'Amount'           => $payment->getAmount(),
                        'Currency'         => $payment->getCurrency(),
                        'FirstName'        => $payment->getPayerFirstName(),
                        'LastName'         => $payment->getPayerLastName(),
                        'CreditCardNumber' => $payment->getCreditCardNumber(),
                        'Cvv'              => $payment->getCreditCardCvv(),
                        'ExpMonth'         => $payment->getCreditCardExpirationMonth(),
                        'ExpYear'          => $payment->getCreditCardExpirationYear(),
                    ],
                ])
            )
            ->will($this->returnValue($response));
    }

    /**
     * Set up URL source: getUrl.
     *
     * @param string $apiUrl
     */
    private function setUpUrlSourceGetUrl(string $apiUrl): void
    {
        $this->urlSource->expects($this->once())
            ->method('getUrl')
            ->with()
            ->will($this->returnValue($apiUrl));
    }
}
