<?php

namespace App\Service\Facades;

use App\Service\ImageSignatureService;
use App\Service\ImageUploaderService;

class ImageFacadeService
{
    /**
     * @var ImageSignatureService
     */
    private $imageSignatureService;
    /**
     * @var ImageUploaderService
     */
    private $imageUploaderService;

    public function __construct(
        ImageSignatureService $imageSignatureService,
        ImageUploaderService $imageUploaderService
    )
    {
        $this->imageSignatureService = $imageSignatureService;
        $this->imageUploaderService = $imageUploaderService;
    }

    public function getImageSignatureService(): ImageSignatureService
    {
        return $this->imageSignatureService;
    }

    public function getImageUploaderService(): ImageUploaderService
    {
        return $this->imageUploaderService;
    }
}