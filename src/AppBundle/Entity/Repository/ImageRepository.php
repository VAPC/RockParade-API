<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Image;
use AppBundle\Entity\Infrasctucture\AbstractRepository;

/** {@inheritDoc} */
class ImageRepository extends AbstractRepository
{

    /**
     * @return Image|null
     */
    public function findOneById($imageId)
    {
        return $this->find($imageId);
    }
}
