<?php

namespace AppBundle\Service\File;

use AppBundle\Entity\Event;
use AppBundle\Entity\Image;
use AppBundle\Entity\Repository\ImageRepository;

/**
 * @author Vehsamrak
 */
class FileService
{

    /** @var ImageRepository */
    private $imageRepository;

    /** @var string */
    private $filePath;

    public function __construct(string $applicationRootPath, ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->filePath = realpath($applicationRootPath . '/../var/upload');
    }

    public function createBase64Image(string $fileName, string $fileContents, $entity)
    {
        $image = new Image($fileName);
        $this->imageRepository->persist($image);

        $imagesPath = $this->filePath . '/images';
        $filePath = sprintf('%s%s%s', $imagesPath, DIRECTORY_SEPARATOR, $fileName);
        file_put_contents($filePath, base64_decode($fileContents));

        if ($entity instanceof Event) {
            $entity->addImage($image);
        }

        $this->imageRepository->flush();
    }
}
