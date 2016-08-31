<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Infrasctucture\AbstractRepository;
use AppBundle\Entity\User;

/** {@inheritDoc} */
class UserRepository extends AbstractRepository
{

    /**
     * @param string $userLogin
     * @return User|object|null
     */
    public function findOneByLogin(string $userLogin)
    {
        return $this->findOneBy(
            [
                'login' => $userLogin,
            ]
        );
    }

    /**
     * @return User|object|null
     */
    public function findOneByName(string $userName)
    {
        return $this->findOneBy(
            [
                'name' => $userName,
            ]
        );
    }

    /**
     * @return User|object|null
     */
    public function findUserByVkId(int $vkId)
    {
        return $this->findOneBy(
            [
                'vkontakteId' => $vkId,
            ]
        );
    }
}
