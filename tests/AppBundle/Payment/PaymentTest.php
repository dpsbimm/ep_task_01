<?php

namespace Tests\AppBundle\Payment;

use AppBundle\Exception\Payment\EmptyFirstNameException;
use AppBundle\Exception\Payment\EmptyLastNameException;
use AppBundle\Exception\Payment\InvalidCreditCardCvvException;
use AppBundle\Exception\Payment\InvalidCreditCardExpirationMonthException;
use AppBundle\Exception\Payment\InvalidCreditCardExpirationYearException;
use AppBundle\Exception\Payment\InvalidCreditCardNumberException;
use AppBundle\Exception\Payment\InvalidCurrencyException;
use AppBundle\Exception\Payment\InvalidPaymentAmountException;
use AppBundle\Payment\Payment;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    /**
     * @var Payment
     */
    private $payment;

    /**
     * PHPUnit: setUp.
     */
    public function setUp(): void
    {
        $this->payment = new Payment(
            123456,
            '123',
            6,
            (date('Y') + 4),
            '1234567890123456',
            'XYZ',
            'Foo',
            'Bauer'
        );
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown(): void
    {
        $this->payment = null;
    }

    /**
     * @param int $amount
     *
     * @dataProvider provideInvalidAmountData
     */
    public function testGetSetAmountExceptionInvalidAmount(int $amount): void
    {
        $this->expectException(InvalidPaymentAmountException::class);

        $this->payment->setAmount($amount);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidAmountData(): array
    {
        return [
            '0' => [
                0,
            ],
            '-1' => [
                -1,
            ],
        ];
    }

    /**
     * @param int $amount
     *
     * @dataProvider provideValidAmountData
     */
    public function testGetSetAmountSuccess(int $amount): void
    {
        $this->assertNotSame($amount, $this->payment->getAmount());

        $this->payment->setAmount($amount);

        $this->assertSame($amount, $this->payment->getAmount());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideValidAmountData(): array
    {
        return [
            'small amount' => [
                11,
            ],
            'medium amount' => [
                54321,
            ],
            'huge amount' => [
                9876543210,
            ],
        ];
    }

    /**
     * @param string $cvv
     *
     * @dataProvider provideInvalidCvvData
     */
    public function testGetSetCreditCardCvvExceptionInvalidCvv(string $cvv): void
    {
        $this->expectException(InvalidCreditCardCvvException::class);

        $this->payment->setCreditCardCvv($cvv);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidCvvData(): array
    {
        return [
            'not numeric' => [
                'abc',
            ],
            'too short (2 digits)' => [
                '12',
            ],
            'too long (5 digits)' => [
                '12345',
            ],
        ];
    }

    /**
     * @param string $cvv
     *
     * @dataProvider provideValidCvvData
     */
    public function testGetSetCreditCardCvvSuccess(string $cvv): void
    {
        $this->assertNotSame($cvv, $this->payment->getCreditCardCvv());

        $this->payment->setCreditCardCvv($cvv);

        $this->assertSame($cvv, $this->payment->getCreditCardCvv());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideValidCvvData(): array
    {
        return [
            '3 digits' => [
                '234',
            ],
            '4 digits' => [
                '1234',
            ],
        ];
    }

    /**
     * @param int $month
     *
     * @dataProvider provideInvalidCreditCardExpirationMonthData
     */
    public function testGetSetCreditCardExpirationMonthExceptionInvalidMonth(int $month): void
    {
        $this->expectException(InvalidCreditCardExpirationMonthException::class);

        $this->payment->setCreditCardExpirationMonth($month);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidCreditCardExpirationMonthData(): array
    {
        return [
            '0' => [
                0,
            ],
            '-1' => [
                -1,
            ],
            '13' => [
                13,
            ],
        ];
    }

    /**
     * @param int $month
     *
     * @dataProvider provideValidCreditCardExpirationMonthData
     */
    public function testGetSetCreditCardExpirationMonthSuccess(int $month): void
    {
        $this->assertNotSame($month, $this->payment->getCreditCardExpirationMonth());

        $this->payment->setCreditCardExpirationMonth($month);

        $this->assertSame($month, $this->payment->getCreditCardExpirationMonth());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideValidCreditCardExpirationMonthData(): array
    {
        return [
            'January' => [
                1,
            ],
            'August' => [
                8,
            ],
            'December' => [
                12,
            ],
        ];
    }

    /**
     * @param int $year
     *
     * @dataProvider provideInvalidCreditCardExpirationYearData
     */
    public function testGetSetCreditCardExpirationYearExceptionInvalidYear(int $year): void
    {
        $this->expectException(InvalidCreditCardExpirationYearException::class);

        $this->payment->setCreditCardExpirationYear($year);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidCreditCardExpirationYearData(): array
    {
        return [
            '1900' => [
                1900,
            ],
            'last year' => [
                (date('Y') - 1),
            ],
        ];
    }

    /**
     * @param int $year
     *
     * @dataProvider provideValidCreditCardExpirationYearData
     */
    public function testGetSetCreditCardExpirationYearSuccess(int $year): void
    {
        $this->assertNotSame($year, $this->payment->getCreditCardExpirationYear());

        $this->payment->setCreditCardExpirationYear($year);

        $this->assertSame($year, $this->payment->getCreditCardExpirationYear());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideValidCreditCardExpirationYearData(): array
    {
        return [
            'current year' => [
                date('Y'),
            ],
            'next year' => [
                (date('Y') + 1),
            ],
            'in 10 years' => [
                (date('Y') + 10),
            ],
        ];
    }

    /**
     * @param string $creditCardNumber
     *
     * @dataProvider provideInvalidCreditCardNumberData
     */
    public function testGetSetCreditCardNumberExceptionInvalidNumber(string $creditCardNumber): void
    {
        $this->expectException(InvalidCreditCardNumberException::class);

        $this->payment->setCreditCardNumber($creditCardNumber);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidCreditCardNumberData(): array
    {
        return [
            'empty' => [
                '',
            ],
            'not just digits' => [
                '12345678901abcd',
            ],
            'too short - 12 digits' => [
                '123456789012',
            ],
            'too long - 20 digits' => [
                '12345678901234567890',
            ],
        ];
    }

    /**
     * @param string $creditCardNumber
     *
     * @dataProvider provideValidCreditCardNumberData
     */
    public function testGetSetCreditCardNumberSuccess(string $creditCardNumber): void
    {
        $this->assertNotSame($creditCardNumber, $this->payment->getCreditCardNumber());

        $this->payment->setCreditCardNumber($creditCardNumber);

        $this->assertSame($creditCardNumber, $this->payment->getCreditCardNumber());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideValidCreditCardNumberData(): array
    {
        return [
            'VISA' => [
                '4242424242424242',
            ],
            'VISA (debit)' => [
                '4000056655665556',
            ],
            'Mastercard' => [
                '5555555555554444',
            ],
            'Mastercard (debit)' => [
                '5200828282828210',
            ],
            'Mastercard (prepaid)' => [
                '5105105105105100',
            ],
            'American Express' => [
                '378282246310005',
            ],
            'American Express #2' => [
                '371449635398431',
            ],
            'Discover' => [
                '6011111111111117',
            ],
            'Discover #2' => [
                '6011000990139424',
            ],
            'Diners Club' => [
                '30569309025904',
            ],
            'Diners Club #2' => [
                '38520000023237',
            ],
            'JCB' => [
                '3530111333300000',
            ],
        ];
    }

    /**
     * @param string $currency
     *
     * @dataProvider provideInvalidCurrencyData
     */
    public function testGetSetCurrencyExceptionInvalidCurrency(string $currency): void
    {
        $this->expectException(InvalidCurrencyException::class);

        $this->payment->setCurrency($currency);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideInvalidCurrencyData(): array
    {
        return [
            'empty' => [
                '',
            ],
            'too short - 2 characters' => [
                'AB',
            ],
            'too long - 4 characters' => [
                'ABCD',
            ],
            'not in capital letters' => [
                'abc',
            ],
        ];
    }

    /**
     * @param string $currency
     *
     * @dataProvider provideValidCurrencyData
     */
    public function testGetSetCurrencySuccess(string $currency): void
    {
        $this->assertNotSame($currency, $this->payment->getCurrency());

        $this->payment->setCurrency($currency);

        $this->assertSame($currency, $this->payment->getCurrency());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function provideValidCurrencyData(): array
    {
        return [
            'EUR' => [
                'EUR',
            ],
            'USD' => [
                'USD',
            ],
        ];
    }

    public function testGetSetPayerFirstNameExceptionEmpty(): void
    {
        $this->expectException(EmptyFirstNameException::class);

        $this->payment->setPayerFirstName('');
    }

    public function testGetSetPayerFirstNameSuccess(): void
    {
        $firstName = 'not empty';

        $this->assertNotSame($firstName, $this->payment->getPayerFirstName());

        $this->payment->setPayerFirstName($firstName);

        $this->assertSame($firstName, $this->payment->getPayerFirstName());
    }

    public function testGetSetPayerLastNameExceptionEmpty(): void
    {
        $this->expectException(EmptyLastNameException::class);

        $this->payment->setPayerLastName('');
    }

    public function testGetSetPayerLastNameSuccess(): void
    {
        $lastName = 'not empty';

        $this->assertNotSame($lastName, $this->payment->getPayerLastName());

        $this->payment->setPayerLastName($lastName);

        $this->assertSame($lastName, $this->payment->getPayerLastName());
    }
}
