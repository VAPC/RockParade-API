<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Vehsamrak
 */
class DefaultControllerTest extends WebTestCase
{

    /** @test */
    public function indexAction_indexPage_greetingsPhraseRenderedInResponse()
    {
        $client = $this->createClient();

        $crawler = $client->request(Request::METHOD_GET, '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Rock Parade!', $crawler->filter('h3')->text());
    }
}
