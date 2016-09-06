<?php

namespace AppBundle\Response;

use AppBundle\Entity\Image;
use AppBundle\Exception\UnsupportedTypeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class ApiResponseFactory
{

    /** @var string */
    private $filePath;

    public function __construct(string $applicationRootPath)
    {
        $this->filePath = realpath($applicationRootPath . '/../var/upload');
    }

    public function createResponse($responseData)
    {
        if ($responseData instanceof Image) {
            $imagesBasePath = $this->filePath . '/images/';
            $imagePath = $imagesBasePath . $responseData->getName();
            $response = new FileResponse($imagePath);
        } else {
            throw new UnsupportedTypeException();
        }

        return $response;
    }

    public function createNotFoundResponse(): ApiError
    {
        return new ApiError('Resource was not found.', Response::HTTP_NOT_FOUND);
    }
}
