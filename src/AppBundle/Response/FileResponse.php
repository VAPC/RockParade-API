<?php

namespace AppBundle\Response;

use AppBundle\Response\Infrastructure\AbstractApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class FileResponse extends AbstractApiResponse
{

    /** @var string */
    private $filePath;

    public function __construct(string $filePath, int $httpCode = null)
    {
        $this->httpCode = $httpCode ?: Response::HTTP_OK;
        $this->filePath = $filePath;
    }

    public function getFile()
    {
        return $this->filePath;
    }
}
