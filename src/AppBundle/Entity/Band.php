<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\Ambassador;
use Doctrine\ORM\Mapping as ORM;

/**
 * Music band (artist, art collective)
 * @ORM\Table(name="bands")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BandRepository")
 */
class Band extends Ambassador
{
}
