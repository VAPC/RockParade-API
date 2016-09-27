<?php

namespace AppBundle\Service\Ambassador;

use AppBundle\Entity\Band;
use AppBundle\Entity\Organizer;
use MyCLabs\Enum\Enum;

/**
 * @author Vehsamrak
 */
class AmbassadorType extends Enum
{
    const BAND = Band::class;
    const ORGANIZER = Organizer::class;
}
