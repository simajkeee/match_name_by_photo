<?php

namespace App\Service\Facades;

use App\Service\FaceMatchingService;
use App\Service\FormValidators\FaceFormValidatorService;

class FaceFacadeService
{
    /**
     * @var FaceMatchingService
     */
    private $faceMatching;
    /**
     * @var FaceFormValidatorService
     */
    private $faceFormValidator;

    public function __construct(
        FaceMatchingService $faceMatching,
        FaceFormValidatorService $faceFormValidator
    )
    {
        $this->faceMatching = $faceMatching;
        $this->faceFormValidator = $faceFormValidator;
    }

    /**
     * @return FaceMatchingService
     */
    public function getFaceMatchingService(): FaceMatchingService
    {
        return $this->faceMatching;
    }

    /**
     * @return FaceFormValidatorService
     */
    public function getFaceFormValidatorService(): FaceFormValidatorService
    {
        return $this->faceFormValidator;
    }


}