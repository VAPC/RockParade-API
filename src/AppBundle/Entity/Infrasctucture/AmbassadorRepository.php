<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Exception\HttpRuntimeException;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\Ambassador\AmbassadorFormType;

/**
 * @author Vehsamrak
 */
class AmbassadorRepository extends AbstractRepository
{

    /**
     * @param AmbassadorFormType $formType
     * @return Ambassador|null|object
     */
    public function findOneByFormData(AbstractFormType $formType)
    {
        if (!($formType instanceof AmbassadorFormType)) {
            throw new HttpRuntimeException(sprintf('Form type "%s" is not supported.', get_class($formType)));
        }

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
