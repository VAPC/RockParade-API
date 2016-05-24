<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\AbstractRepository;

/** {@inheritDoc} */
class UserRepository extends AbstractRepository
{

    /**
     * @param string $userLogin
     * @return User|null
     */
    public function findOneByLogin(string $userLogin)
    {
        return parent::findOneByLogin($userLogin);
    }

    /**
     * @param string $userName
     * @return User|null
     */
    public function findOneByName(string $userName)
    {
        return parent::findOneByName($userName);
    }
}
