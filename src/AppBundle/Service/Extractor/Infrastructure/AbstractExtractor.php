<?php

namespace AppBundle\Service\Extractor\Infrastructure;

use Symfony\Component\Routing\Router;

/**
 * @author Vehsamrak
 */
abstract class AbstractExtractor implements ExtractorInterface
{

    /** @var Router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    protected function generateUrl(string $route, array $parameters = []): string
    {
        return $this->router->generate($route, $parameters, Router::ABSOLUTE_URL);
    }
}
