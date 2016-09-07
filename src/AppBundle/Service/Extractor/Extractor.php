<?php

namespace AppBundle\Service\Extractor;

use AppBundle\Exception\ExtractorNotExists;
use AppBundle\Service\Extractor\Infrastructure\ExtractorInterface;
use Symfony\Component\Routing\Router;

/**
 * @author Vehsamrak
 */
class Extractor
{

    /** @var Router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param object $entity Entity object
     * @throws ExtractorNotExists
     */
    public function extract($entity)
    {
        $entityClassName = (new \ReflectionClass(get_class($entity)))->getShortName();
        $extractorClassName = __NAMESPACE__ . '\\' . $entityClassName . 'Extractor';

        if (!class_exists($extractorClassName)) {
            throw new ExtractorNotExists();
        }

        /** @var ExtractorInterface $extractor */
        $extractor = new $extractorClassName($this->router);

        return $extractor->extract($entity);
    }
}
