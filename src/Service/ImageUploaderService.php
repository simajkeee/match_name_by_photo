<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploaderService
{
    /**
     * @var ImageSignatureService
     */
    private $imageSignature;


    public function __construct(ImageSignatureService $imageSignature)
    {
        $this->imageSignature = $imageSignature;
    }

    public function move(string $uploadPath, UploadedFile $file)
    {
        $filename = $this->imageSignature->getSignatureAndExtension($file);
        if (!file_exists($uploadPath . '/' . $filename)) {
            $file->move($uploadPath, $filename);
        }
    }
}