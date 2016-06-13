<?php

namespace Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    protected function sendGetRequest(string $route)
    {
        $this->httpClient->request(Request::METHOD_GET, $route);
    }

    protected function sendPostRequest(string $route, array $parameters = [])
    {
        $this->httpClient->request(Request::METHOD_POST, $route, $parameters);
    }
    
    protected function sendPutRequest(string $route, array $parameters = [])
    {
        $this->httpClient->request(Request::METHOD_PUT, $route, $parameters);
    }

    /**
     * @return array|string
     * @throws \Exception
     */
    protected function getResponseContents()
    {
        $response = $this->getResponse();
        $responseContents = $response->getContent();

        $jsonEncodedResponseContent = json_decode($responseContents, true);

        if ($jsonEncodedResponseContent) {
            return $jsonEncodedResponseContent;
        } elseif ($responseContents) {
            throw new \Exception('Response contents: "' . $responseContents . '"');
        } else {
            return $responseContents;
        }
    }

    protected function getResponseCode(): int
    {
        return $this->getResponse()->getStatusCode();
    }

    /**
     * @return Response|null
     */
    protected function getResponse()
    {
        return $this->httpClient->getResponse();
    }

    /**
     * @return string|null
     */
    protected function getResponseLocation()
    {
        return $this->getResponse()->headers->get('Location');
    }
}
