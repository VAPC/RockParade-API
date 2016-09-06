<?php

namespace AppBundle\Service\File;

use AppBundle\Exception\UnsupportedTypeException;

/**
 * @author Vehsamrak
 */
class ImageExtensionChecker
{

    /**
     * @throws UnsupportedTypeException
     */
    public function getExtensionFromBase64File(string $image): string
    {
        $imageMap = [
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/jpeg' => 'jpeg',
        ];

        $imagedata = base64_decode($image);
        $image = finfo_open();
        $mimeType = finfo_buffer($image, $imagedata, FILEINFO_MIME_TYPE);

        if (!array_key_exists($mimeType, $imageMap)) {
        	throw new UnsupportedTypeException();
        }

        return $imageMap[$mimeType];
    }
}
