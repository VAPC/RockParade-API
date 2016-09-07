<?php

namespace AppBundle\Service\Extractor\Infrastructure;

/**
 * @author Vehsamrak
 */
interface ExtractorInterface
{

    /**
     * @param object $entity Entity object
     */
    public function extract($entity): array;
}
