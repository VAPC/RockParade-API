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
    const TEST_TOKEN = 'test-token';

    /** @var Client */
    protected $httpClient;

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->httpClient = $this->makeClient(
            false,
            [
                'HTTP_AUTH_TOKEN' => self::TEST_TOKEN,
            ]
        );
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

    protected function sendDeleteRequest(string $route, array $parameters = [])
    {
        $this->httpClient->request(Request::METHOD_DELETE, $route, $parameters);
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

    protected function setAuthToken(string $token)
    {
        $this->httpClient->setServerParameter('HTTP_AUTH_TOKEN', $token);
    }

    /**
     * Get last created entity from database
     * @param string $entityClass
     * @return mixed
     */
    protected function getLastCreated(string $entityClass)
    {
        $repository = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository($entityClass);
        $allEntities = $repository->findAll();
        $entity = array_pop($allEntities);

        return $entity;
    }
}
