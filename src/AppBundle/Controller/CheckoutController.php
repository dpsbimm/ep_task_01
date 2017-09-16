<?php

namespace AppBundle\Controller;

use AppBundle\Exception\Payment\InvalidPaymentInformationException;
use AppBundle\Exception\RemoteApi\RemoteApiException;
use AppBundle\Payment\Payment;
use AppBundle\RemoteApi\RemotePaymentProcessorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController extends Controller
{
    /**
     * @Route("/checkout", name="checkout_get")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function checkoutGetAction(): Response
    {
        return $this->render('checkout/checkout.html.twig');
    }

    /**
     * @Route("/checkout", name="checkout_post")
     * @Method({"POST"})
     *
     * @param Request                         $request
     * @param RemotePaymentProcessorInterface $remotePaymentProcessor
     *
     * @return Response
     */
    public function checkoutPostAction(
        Request $request,
        RemotePaymentProcessorInterface $remotePaymentProcessor
    ): Response {
        $amount = (int) floor((float) $request->request->get('amount') * 100);

        try {
            $payment = new Payment(
                $amount,
                $request->request->get('creditcard_cvv', ''),
                $request->request->getInt('creditcard_expiration_month'),
                $request->request->getInt('creditcard_expiration_year'),
                $request->request->get('creditcard_number', ''),
                $request->request->get('currency', ''),
                $request->request->get('payer_first_name', ''),
                $request->request->get('payer_last_name', '')
            );
        } catch (InvalidPaymentInformationException $e) {
            return $this->render(
                'checkout/checkout_error.html.twig',
                ['errorMessage' => $e->getMessage()]
            );
        }

        try {
            $response = $remotePaymentProcessor->sendPaymentRequest($payment);
        } catch (RemoteApiException $e) {
            return $this->render('checkout/checkout_error.html.twig');
        }

        if (1 === $response['resultCode']) {
            return $this->render(
                'checkout/checkout_success.html.twig',
                ['transactionId' => $response['id']]
            );
        } else {
            return $this->render(
                'checkout/checkout_error.html.twig',
                ['errorMessage' => $response['resultMessage']]
            );
        }
    }
}
