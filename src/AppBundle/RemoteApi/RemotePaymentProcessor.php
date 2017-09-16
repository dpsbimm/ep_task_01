<?php

namespace AppBundle\RemoteApi;

use AppBundle\Exception\RemoteApi\InvalidResponseException;
use AppBundle\Exception\RemoteApi\UnexpectedResponseStatusException;
use AppBundle\Payment\Payment;
use GuzzleHttp\ClientInterface;

class RemotePaymentProcessor implements RemotePaymentProcessorInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RemotePaymentUrlSourceInterface
     */
    private $urlSource;

    /**
     * Constructor.
     *
     * @param ClientInterface                 $client
     * @param RemotePaymentUrlSourceInterface $urlSource
     */
    public function __construct(ClientInterface $client, RemotePaymentUrlSourceInterface $urlSource)
    {
        $this->client = $client;
        $this->urlSource = $urlSource;
    }

    /**
     * @inheritDoc
     */
    public function sendPaymentRequest(Payment $payment): array
    {
        $response = $this->client->request(
            'GET',
            $this->urlSource->getUrl(),
            [
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
            ]
        );

        if (200 !== $response->getStatusCode()) {
            throw new UnexpectedResponseStatusException($response->getStatusCode());
        }

        $rawBody = (string) $response->getBody();

        $responseData = json_decode($rawBody, true);

        if ((null === $responseData)
            || !array_key_exists('result', $responseData)
            || !array_key_exists('resultCode', $responseData)
        ) {
            throw new InvalidResponseException();
        }

        return $responseData;
    }
}
