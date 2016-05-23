<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/** {@inheritDoc} */
class UserRepository extends EntityRepository
{

    /**
     * @param string $userLogin
     * @return User|null
     */
    public function findOneByLogin(string $userLogin)
    {
        return parent::findOneByLogin($userLogin);
    }
}
