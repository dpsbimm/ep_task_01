<?php

namespace Tests\AppBundle\RemoteApi;

use AppBundle\Exception\RemoteApi\InvalidResponseException;
use AppBundle\Exception\RemoteApi\UnexpectedResponseStatusException;
use AppBundle\Payment\Payment;
use AppBundle\RemoteApi\RemotePaymentProcessor;
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
     * @var string
     */
    private $paymentApiUrl;

    /**
     * @var RemotePaymentProcessor
     */
    private $processor;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->client = $this->createClientInterfaceMock();
        $this->paymentApiUrl = 'http://localhost/remote-payments/pay/';

        $this->processor = new RemotePaymentProcessor($this->client, $this->paymentApiUrl);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->processor = null;
        $this->paymentApiUrl = null;
        $this->client = null;
    }

    public function testSendPaymentRequestExceptionUnexpectedResponseStatus()
    {
        $this->expectException(UnexpectedResponseStatusException::class);

        $payment = $this->generateExamplePayment();

        $prepResponse = new Response(500);

        $this->setUpClientRequest($payment, $prepResponse);

        $this->processor->sendPaymentRequest($payment);
    }

    /**
     * @param string $responseBody
     *
     * @dataProvider provideInvalidResponseBodyData
     */
    public function testSendPaymentRequestExceptionInvalidResponse(string $responseBody)
    {
        $this->expectException(InvalidResponseException::class);

        $payment = $this->generateExamplePayment();

        $prepResponse = new Response(200, [], $responseBody);

        $this->setUpClientRequest($payment, $prepResponse);

        $this->processor->sendPaymentRequest($payment);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidResponseBodyData()
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
    public function testSendPaymentRequestSuccess(array $responseData)
    {
        $payment = $this->generateExamplePayment();

        $prepResponse = new Response(200, [], json_encode($responseData));

        $this->setUpClientRequest($payment, $prepResponse);

        $response = $this->processor->sendPaymentRequest($payment);

        $this->assertSame($responseData, $response);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideValidResponseBodyData()
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
     * @param Response $response
     */
    private function setUpClientRequest(Payment $payment, Response $response): void
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with(
                $this->identicalTo('GET'),
                $this->identicalTo($this->paymentApiUrl),
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
}
