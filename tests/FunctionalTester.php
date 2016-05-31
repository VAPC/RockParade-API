<?php

namespace Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;

/**
 * @author Vehsamrak
 */
abstract class FunctionalTester extends WebTestCase
{
    /** @var Client */
    protected $httpClient;

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->httpClient = $this->makeClient();
    }

    /** {@inheritDoc} */
    protected function tearDown()
    {
        $this->httpClient = null;
        parent::tearDown();
    }

    protected function followRedirects()
    {
        $this->httpClient->followRedirects();
    }

    /**
     * @param string $route
     * @throws \Exception
     */
    protected function sendGetRequest(string $route)
    {
        $this->httpClient->request(Request::METHOD_GET, $route);
    }

    /**
     * @param string $route
     * @param array $parameters
     */
    protected function sendPostRequest(string $route, array $parameters = [])
    {
        $this->httpClient->request(Request::METHOD_POST, $route, $parameters);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getResponseContents(): array
    {
        $response = $this->httpClient->getResponse();
        $responseContent = $response->getContent();

        $jsonEncodedResponseContent = json_decode($responseContent, true);

        if ($jsonEncodedResponseContent) {
            return $jsonEncodedResponseContent;
        } else {
            throw new \Exception('Response content: "' . $responseContent . '"');
        }
    }

    /**
     * @return int
     */
    protected function getResponseCode()
    {
        $responseCode = $this->httpClient->getResponse()->getStatusCode();

        return $responseCode;
    }
}
