<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\Ambassador\AmbassadorFormType;

/**
 * @author Vehsamrak
 */
class AmbassadorRepository extends AbstractRepository
{

    /**
     * @return Ambassador|object|null
     */
    public function findOneByName(string $name)
    {
        return $this->findOneBy(
            [
                'name' => $name,
            ]
        );
    }

    /**
     * @param AmbassadorFormType $formType
     * @param User $creator
     * @return Ambassador|null|object
     */
    public function findOneByFormData(AbstractFormType $formType)
    {
        $ambassadorName = $formType->name;
        $ambassador = $this->findOneByName($ambassadorName);

        return $ambassador;
    }
}
