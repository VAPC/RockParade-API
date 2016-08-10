<?php

namespace AppBundle\Entity\Infrasctucture;

/**
 * @author Vehsamrak
 */
trait FormattedRegistrationDateTrait
{
    /**
     * @return string
     */
    public function getRegistrationDate(): string
    {
        return $this->registrationDate->format('Y-m-d H:i:s');
    }
}