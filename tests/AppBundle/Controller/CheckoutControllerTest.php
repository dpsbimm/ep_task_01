<?php

namespace Tests\AppBundle\Controller;

use AppBundle\RemoteApi\RemotePaymentUrlSourceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class CheckoutControllerTest extends WebTestCase
{
    public function testCheckoutGetActionSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/checkout');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Checkout', $crawler->filter('#container h1')->text());
    }

    /**
     * @param array $postValues
     * @param string $expErrorMessage
     *
     * @dataProvider provideInvalidInputData
     */
    public function testCheckoutPostActionSuccessErrorInvalidInputData(array $postValues, string $expErrorMessage)
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/checkout', $postValues);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSame('Checkout Error', $crawler->filter('#container h1')->text());
        $this->assertSame($expErrorMessage, $crawler->filter('#container p.error')->text());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidInputData()
    {
        $baseDataSet = [
            $this->getValidInputDataSet(),
            '',
        ];

        $dataSets = [];

        $dataSet = $baseDataSet;
        unset($dataSet[0]['amount']);
        $dataSet[1] = 'Invalid payment amount "0"';
        $dataSets['amount - missing'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['amount'] = '';
        $dataSet[1] = 'Invalid payment amount "0"';
        $dataSets['amount - empty'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['amount'] = 0;
        $dataSet[1] = 'Invalid payment amount "0"';
        $dataSets['amount = 0'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['amount'] = -1;
        $dataSet[1] = 'Invalid payment amount "-100"';
        $dataSets['amount < 0'] = $dataSet;

        $dataSet = $baseDataSet;
        unset($dataSet[0]['creditcard_cvv']);
        $dataSet[1] = 'Invalid credit card CVV ""';
        $dataSets['credit card CVV - missing'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_cvv'] = '';
        $dataSet[1] = 'Invalid credit card CVV ""';
        $dataSets['credit card CVV - empty'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_cvv'] = '12';
        $dataSet[1] = 'Invalid credit card CVV "12"';
        $dataSets['credit card CVV - too short'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_cvv'] = '12345';
        $dataSet[1] = 'Invalid credit card CVV "12345"';
        $dataSets['credit card CVV - too long'] = $dataSet;

        $dataSet = $baseDataSet;
        unset($dataSet[0]['creditcard_expiration_month']);
        $dataSet[1] = 'Invalid credit card expiration month "0"';
        $dataSets['credit card expiration month - missing'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_expiration_month'] = '';
        $dataSet[1] = 'Invalid credit card expiration month "0"';
        $dataSets['credit card expiration month - empty'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_expiration_month'] = 0;
        $dataSet[1] = 'Invalid credit card expiration month "0"';
        $dataSets['credit card expiration month = 0'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_expiration_month'] = 13;
        $dataSet[1] = 'Invalid credit card expiration month "13"';
        $dataSets['credit card expiration month = 13'] = $dataSet;

        $dataSet = $baseDataSet;
        unset($dataSet[0]['creditcard_expiration_year']);
        $dataSet[1] = 'Invalid credit card expiration year "0"';
        $dataSets['credit card expiration year - missing'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_expiration_year'] = '';
        $dataSet[1] = 'Invalid credit card expiration year "0"';
        $dataSets['credit card expiration year - empty'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_expiration_year'] = date('Y') - 1;
        $dataSet[1] = sprintf('Invalid credit card expiration year "%s"', date('Y') - 1);
        $dataSets['credit card expiration year < current year'] = $dataSet;

        $dataSet = $baseDataSet;
        unset($dataSet[0]['creditcard_number']);
        $dataSet[1] = 'Invalid credit card number ""';
        $dataSets['credit card number - missing'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_number'] = '';
        $dataSet[1] = 'Invalid credit card number ""';
        $dataSets['credit card number - empty'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_number'] = '123456789012';
        $dataSet[1] = 'Invalid credit card number "123456789012"';
        $dataSets['credit card number - too short'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_number'] = '12345678901234567890';
        $dataSet[1] = 'Invalid credit card number "12345678901234567890"';
        $dataSets['credit card number - too long'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['creditcard_number'] = '123456789012345X';
        $dataSet[1] = 'Invalid credit card number "123456789012345X"';
        $dataSets['credit card number - invalid characters'] = $dataSet;

        $dataSet = $baseDataSet;
        unset($dataSet[0]['currency']);
        $dataSet[1] = 'Invalid currency ""';
        $dataSets['currency - missing'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['currency'] = '';
        $dataSet[1] = 'Invalid currency ""';
        $dataSets['currency - empty'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['currency'] = 'AB';
        $dataSet[1] = 'Invalid currency "AB"';
        $dataSets['currency - invalid length'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['currency'] = 'abc';
        $dataSet[1] = 'Invalid currency "abc"';
        $dataSets['currency - not all letters are upper case'] = $dataSet;

        $dataSet = $baseDataSet;
        unset($dataSet[0]['payer_first_name']);
        $dataSet[1] = 'First name is empty';
        $dataSets['payer first name - missing'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['payer_first_name'] = '';
        $dataSet[1] = 'First name is empty';
        $dataSets['payer first name - empty'] = $dataSet;

        $dataSet = $baseDataSet;
        unset($dataSet[0]['payer_last_name']);
        $dataSet[1] = 'Last name is empty';
        $dataSets['payer last name - missing'] = $dataSet;

        $dataSet = $baseDataSet;
        $dataSet[0]['payer_last_name'] = '';
        $dataSet[1] = 'Last name is empty';
        $dataSets['payer last name - empty'] = $dataSet;

        return $dataSets;
    }

    /**
     * @param string   $urlSourceKey
     * @param array    $postValues
     * @param string   $expTitle
     * @param callable $crawlerAssertions
     *
     * @dataProvider provideCheckoutPostData
     */
    public function testCheckoutPostActionSuccess(
        string $urlSourceKey,
        array $postValues,
        string $expTitle,
        callable $crawlerAssertions
    ) {
        $client = self::createClient();

        $urlSource = $client->getContainer()->get(RemotePaymentUrlSourceInterface::class);
        $urlSource->setUrl($urlSourceKey);

        $crawler = $client->request('POST', '/checkout', $postValues);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSame($expTitle, $crawler->filter('#container h1')->text());

        $crawlerAssertions($crawler);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideCheckoutPostData()
    {
        return [
            'invalid JSON' => [
                'dev.invalidjs',
                $this->getValidInputDataSet(),
                'Checkout Error',
                function () {
                },
            ],
            'result = OK' => [
                'dev.ok',
                $this->getValidInputDataSet(),
                'Checkout Successful',
                function (Crawler $crawler) {
                    $this->assertSame(
                        'This transaction has the ID 123.',
                        $crawler->filter('#container p')->last()->text()
                    );
                },
            ],
            'result = DECLINE' => [
                'dev.decline.amount_exceeded',
                $this->getValidInputDataSet(),
                'Checkout Error',
                function (Crawler $crawler) {
                    $this->assertSame('Amount exceed', $crawler->filter('#container p.error')->text());
                },
            ],
        ];
    }

    /**
     * Get valid input data set.
     *
     * @return array
     */
    private function getValidInputDataSet(): array
    {
        return [
            'amount' => 12345,
            'creditcard_cvv' => '123',
            'creditcard_expiration_month' => 7,
            'creditcard_expiration_year' => date('Y') + 2,
            'creditcard_number' => '1234567890123456',
            'currency' => 'EUR',
            'payer_first_name' => 'John',
            'payer_last_name' => 'Doe',
        ];
    }
}
