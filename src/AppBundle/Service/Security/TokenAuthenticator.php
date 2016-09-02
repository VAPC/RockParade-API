<?php

namespace AppBundle\Service\Security;

use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * @author Vehsamrak
 */
class TokenAuthenticator extends AbstractGuardAuthenticator
{

    const TOKEN_HEADER = 'AUTH-TOKEN';

    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /** {@inheritDoc} */
    public function getCredentials(Request $request)
    {
        $token = $request->headers->get(self::TOKEN_HEADER);

        if (!$token) {
            return null;
        }

        return [
            'token' => $token,
        ];
    }

    /** {@inheritDoc} */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $credentials['token'];

        $user = $this->userRepository->findUserByToken($token);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException(
                sprintf('User with token "%s" was not found.', $token)
            );
        }

        return $user;
    }

    /** {@inheritDoc} */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'errors' => [
                sprintf('Authentication required. Use header "%s" with user token.', self::TOKEN_HEADER),
            ],
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /** {@inheritDoc} */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'errors' => [
                strtr($exception->getMessageKey(), $exception->getMessageData()),
            ],
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /** {@inheritDoc} */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /** {@inheritDoc} */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /** {@inheritDoc} */
    public function supportsRememberMe()
    {
        return false;
    }
}
