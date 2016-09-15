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
     * @param AmbassadorFormType $formType
     * @param User $creator
     * @return Ambassador|null|object
     */
    public function findOneByFormData(AbstractFormType $formType)
    {
        /** @var AmbassadorRepository $repository */
        $repository = $this->getEntityManager()->getRepository($formType->getEntityClassName());

        $ambassadorName = $formType->name;
        $ambassador = $repository->findOneByName($ambassadorName);

        return $ambassador;
    }

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
}
