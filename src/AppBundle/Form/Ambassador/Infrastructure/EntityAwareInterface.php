<?php

namespace AppBundle\Form\Ambassador\Infrastructure;

/**
 * @author Vehsamrak
 */
interface EntityAwareInterface
{

    /**
     * Entity class name getter
     * @return string
     */
    public function getEntityClassName(): string;
}
