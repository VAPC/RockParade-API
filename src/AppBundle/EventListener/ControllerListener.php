<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use AppBundle\Enum\Environment;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Vehsamrak
 */
class ControllerListener
{

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var Logger */
    private $logger;

    /** @var string */
    private $environment;

    public function __construct(TokenStorageInterface $tokenStorage, Logger $logger, string $environment)
    {
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
        $this->environment = $environment;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         * Do not process on test environment
         */
        if (!is_array($controller) || $this->environment === Environment::TEST) {
            return;
        }

        /** @var User|string $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $request = $event->getRequest();

        $message = sprintf(
            '[%s] %s %s',
            $user instanceof User ? $user->getLogin() : $request->getClientIp(),
            $request->getMethod(),
            $request->getPathInfo()
        );

        $this->logger->addInfo($message);
    }
}
