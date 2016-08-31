<?php

namespace AppBundle\Service\Security;

use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Vehsamrak
 */
class UserProvider implements UserProviderInterface
{

    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /** {@inheritDoc} */
    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->findOneByLogin($username);

        if (!$user) {
            throw new UsernameNotFoundException(
                sprintf('User with login "%s" does not exist.', $username)
            );
        }

        return $user;
    }

    /** {@inheritDoc} */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /** {@inheritDoc} */
    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
