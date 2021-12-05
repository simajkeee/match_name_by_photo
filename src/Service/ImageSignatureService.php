<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\File;

class ImageSignatureService
{
    /**
     * @var ImageInitializerService
     */
    private $imageInitializerService;

    public function __construct(ImageInitializerService $imageInitializerService)
    {
        $this->imageInitializerService = $imageInitializerService;
    }

    public function getSignature($image)
    {
        return $this->imageInitializerService->initImage($image)->getImageSignature();
    }

    public function getSignatureAndExtension(File $file): string
    {
        return $this->getSignature($file->getRealPath()) . '.' . $file->guessExtension();
    }
}