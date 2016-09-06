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
    private $imagesPath;

    public function __construct(string $applicationRootPath, ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->imagesPath = $applicationRootPath . '/../var/upload/images';

        if (!is_dir($this->imagesPath)) {
            mkdir($this->imagesPath, 0755, true);
        }
    }

    public function createBase64Image(string $fileName, string $fileContents, $entity): Image
    {
        $image = new Image($fileName);
        $this->imageRepository->persist($image);

        $filePath = sprintf('%s%s%s', $this->imagesPath, DIRECTORY_SEPARATOR, $image->getName());
        file_put_contents($filePath, base64_decode($fileContents));

        if ($entity instanceof Event) {
            $entity->addImage($image);
        }

        $this->imageRepository->flush();

        return $image;
    }
}
