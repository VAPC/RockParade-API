<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\Ambassador;
use Doctrine\ORM\Mapping as ORM;

/**
 * Organizer
 * @ORM\Table(name="organizers")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\OrganizerRepository")
 */
class Organizer extends Ambassador
{

}

