<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CheckoutController extends Controller
{
    /**
     * @Route("/checkout", name="checkout_get")
     * @Method({"GET"})
     */
    public function checkoutGetAction()
    {
        return $this->render('checkout/checkout.html.twig');
    }
}
