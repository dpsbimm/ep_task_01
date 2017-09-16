<?php

namespace AppBundle\Payment;

use AppBundle\Exception\Payment\EmptyFirstNameException;
use AppBundle\Exception\Payment\EmptyLastNameException;
use AppBundle\Exception\Payment\InvalidCreditCardCvvException;
use AppBundle\Exception\Payment\InvalidCreditCardExpirationMonthException;
use AppBundle\Exception\Payment\InvalidCreditCardExpirationYearException;
use AppBundle\Exception\Payment\InvalidCreditCardNumberException;
use AppBundle\Exception\Payment\InvalidCurrencyException;
use AppBundle\Exception\Payment\InvalidPaymentAmountException;

class Payment
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $creditCardCvv;

    /**
     * @var int
     */
    private $creditCardExpirationMonth;

    /**
     * @var int
     */
    private $creditCardExpirationYear;

    /**
     * @var string
     */
    private $creditCardNumber;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $payerFirstName;

    /**
     * @var string
     */
    private $payerLastName;

    /**
     * Constructor.
     *
     * @param int    $amount
     * @param string $creditCardCvv
     * @param int    $creditCardExpirationMonth
     * @param int    $creditCardExpirationYear
     * @param string $creditCardNumber
     * @param string $currency
     * @param string $payerFirstName
     * @param string $payerLastName
     */
    public function __construct(
        $amount,
        $creditCardCvv,
        $creditCardExpirationMonth,
        $creditCardExpirationYear,
        $creditCardNumber,
        $currency,
        $payerFirstName,
        $payerLastName
    ) {
        $this->setAmount($amount);
        $this->setCreditCardCvv($creditCardCvv);
        $this->setCreditCardExpirationMonth($creditCardExpirationMonth);
        $this->setCreditCardExpirationYear($creditCardExpirationYear);
        $this->setCreditCardNumber($creditCardNumber);
        $this->setCurrency($currency);
        $this->setPayerFirstName($payerFirstName);
        $this->setPayerLastName($payerLastName);
    }

    /**
     * Getter: amount.
     *
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Setter: amount.
     *
     * @param int $amount
     *
     * @throws \AppBundle\Exception\Payment\InvalidPaymentAmountException
     */
    public function setAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidPaymentAmountException($amount);
        }

        $this->amount = $amount;
    }

    /**
     * Getter: creditCardCvv.
     *
     * @return string
     */
    public function getCreditCardCvv(): string
    {
        return $this->creditCardCvv;
    }

    /**
     * Setter: creditCardCvv.
     *
     * @param string $creditCardCvv
     *
     * @throws \AppBundle\Exception\Payment\InvalidCreditCardCvvException
     */
    public function setCreditCardCvv(string $creditCardCvv): void
    {
        if (!preg_match('#^\\d{3,4}$#', $creditCardCvv)) {
            throw new InvalidCreditCardCvvException($creditCardCvv);
        }

        $this->creditCardCvv = $creditCardCvv;
    }

    /**
     * Getter: creditCardExpirationMonth.
     *
     * @return int
     */
    public function getCreditCardExpirationMonth(): int
    {
        return $this->creditCardExpirationMonth;
    }

    /**
     * Setter: creditCardExpirationMonth.
     *
     * @param int $creditCardExpirationMonth
     *
     * @throws \AppBundle\Exception\Payment\InvalidCreditCardExpirationMonthException
     */
    public function setCreditCardExpirationMonth(int $creditCardExpirationMonth): void
    {
        if (($creditCardExpirationMonth < 1) || ($creditCardExpirationMonth > 12)) {
            throw new InvalidCreditCardExpirationMonthException($creditCardExpirationMonth);
        }

        $this->creditCardExpirationMonth = $creditCardExpirationMonth;
    }

    /**
     * Getter: creditCardExpirationYear.
     *
     * @return int
     */
    public function getCreditCardExpirationYear(): int
    {
        return $this->creditCardExpirationYear;
    }

    /**
     * Setter: creditCardExpirationYear.
     *
     * @param int $creditCardExpirationYear
     *
     * @throws InvalidCreditCardExpirationYearException
     */
    public function setCreditCardExpirationYear(int $creditCardExpirationYear): void
    {
        if ($creditCardExpirationYear < date('Y')) {
            throw new InvalidCreditCardExpirationYearException($creditCardExpirationYear);
        }

        $this->creditCardExpirationYear = $creditCardExpirationYear;
    }

    /**
     * Getter: creditCardNumber.
     *
     * @return string
     */
    public function getCreditCardNumber(): string
    {
        return $this->creditCardNumber;
    }

    /**
     * Setter: creditCardNumber.
     *
     * @param string $creditCardNumber
     *
     * @throws \AppBundle\Exception\Payment\InvalidCreditCardNumberException
     */
    public function setCreditCardNumber(string $creditCardNumber): void
    {
        if (!preg_match('#^\\d{13,19}$#', $creditCardNumber)) {
            throw new InvalidCreditCardNumberException($creditCardNumber);
        }

        $this->creditCardNumber = $creditCardNumber;
    }

    /**
     * Getter: currency.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Setter: currency.
     *
     * @param string $currency
     *
     * @throws \AppBundle\Exception\Payment\InvalidCurrencyException
     */
    public function setCurrency(string $currency): void
    {
        if (($currency !== mb_strtoupper($currency)) || (3 !== mb_strlen($currency))) {
            throw new InvalidCurrencyException($currency);
        }

        $this->currency = $currency;
    }

    /**
     * Getter: payerFirstName.
     *
     * @return string
     */
    public function getPayerFirstName(): string
    {
        return $this->payerFirstName;
    }

    /**
     * Setter: payerFirstName.
     *
     * @param string $payerFirstName
     *
     * @throws EmptyFirstNameException
     */
    public function setPayerFirstName(string $payerFirstName): void
    {
        if (0 === mb_strlen($payerFirstName)) {
            throw new EmptyFirstNameException();
        }

        $this->payerFirstName = $payerFirstName;
    }

    /**
     * Getter: payerLastName.
     *
     * @return string
     */
    public function getPayerLastName(): string
    {
        return $this->payerLastName;
    }

    /**
     * Setter: payerLastName.
     *
     * @param string $payerLastName
     *
     * @throws EmptyLastNameException
     */
    public function setPayerLastName(string $payerLastName): void
    {
        if (0 === mb_strlen($payerLastName)) {
            throw new EmptyLastNameException();
        }

        $this->payerLastName = $payerLastName;
    }
}
