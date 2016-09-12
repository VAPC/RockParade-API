<?php

namespace AppBundle\Enum;

use MyCLabs\Enum\Enum;

/**
 * @author Vehsamrak
 * @method static AmbassadorType BAND()
 * @method static AmbassadorType ORGANIZER()
 */
class AmbassadorType extends Enum
{

    const BAND = 'band';
    const ORGANIZER = 'organizer';
}
