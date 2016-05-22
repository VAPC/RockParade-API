<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** {@inheritDoc} */
class DefaultControllerTest extends WebTestCase
{

    /** @test */
    public function indexAction_indexPage_greetingsPhraseRenderedInResponse()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Rock Parade!', $crawler->filter('h3')->text());
    }
}
