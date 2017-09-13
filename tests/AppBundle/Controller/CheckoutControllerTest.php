<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    public function testCheckoutGetActionSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/checkout');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Checkout', $crawler->filter('#container h1')->text());
    }
}
