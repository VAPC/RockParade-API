<?php

namespace AppBundle\Entity\Infrasctucture;

/**
 * @author Vehsamrak
 */
trait FormattedDateTrait
{
    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->registrationDate->format('Y-m-d H:i:s');
    }
}