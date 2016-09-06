<?php

namespace AppBundle\EventListener;

use AppBundle\Enum\Environment;
use AppBundle\Service\HashGenerator;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @author Vehsamrak
 */
class ExceptionListener
{

    /** @var string */
    private $environment;

    /** @var string */
    private $adminEmail;

    /** @var Logger */
    private $logger;

    public function __construct(string $environment, string $adminEmail, Logger $logger)
    {
        $this->environment = $environment;
        $this->adminEmail = $adminEmail;
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /** @var \Exception $exception */
        $exception = $event->getException();

        if ($this->environment === Environment::PRODUCTION && $exception instanceof HttpExceptionInterface && $exception->getStatusCode() >= 500) {
            $bugSerialNumber = HashGenerator::generate();

            $this->logger->info(
                sprintf(
                    '#%s - %s in %s at line %s',
                    $bugSerialNumber,
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                )
            );

            $errorMessage = sprintf(
                'Wow! You probably found a bug with serial number #%s. Please report it to %s.',
                $bugSerialNumber,
                $this->adminEmail
            );
        } else {
            $errorMessage = sprintf(
                '%s in %s at line %s',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
        }

        $data = [
            'errors' => [
                $errorMessage,
            ],
        ];

        $response = new JsonResponse();
        $response->setContent(json_encode($data));

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
