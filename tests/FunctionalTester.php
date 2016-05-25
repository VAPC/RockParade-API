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
     * @return array
     */
    protected function sendGetRequestAndHandleResponse(string $route): array
    {
        $this->httpClient->request(Request::METHOD_GET, $route);
        $response = $this->httpClient->getResponse();

        return json_decode($response->getContent(), true);
    }

    /**
     * @param string $route
     * @param array $parameters
     */
    protected function sendPostRequest(string $route, array $parameters = [])
    {
        $this->httpClient->request(Request::METHOD_POST, $route, $parameters);
    }

    protected function getResponseContents()
    {
        return json_decode($this->httpClient->getResponse()->getContent(), true);
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
