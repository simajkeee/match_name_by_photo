<?php

namespace App\Service;

class ImageComparatorService
{

    /**
     * @var ImageInitializerService
     */
    private $imageInitializerService;

    public function __construct(ImageInitializerService $imageInitializerService)
    {
        $this->imageInitializerService = $imageInitializerService;
    }

    /**
     * @param string|\Imagick $img1
     * @param string|\Imagick $img2
     * @return bool
     */
    public function areEqual($img1, $img2): bool
    {
        $cmpRes = $this->compare($this->initImage($img1), $this->initImage($img2));
        return !(is_numeric($cmpRes) && $cmpRes > 0) ?? false;
    }

    /**
     * @param $img1
     * @param $img2
     * @return false|numeric
     */
    public function compare($img1, $img2)
    {
        $result = false;
        try {
            $result = $this->performCompare($this->initImage($img1), $this->initImage($img2));
        } catch(\Exception $e) {
        }
        return $result;
    }

    private function performCompare($image1, $image2)
    {
        $result = $image1->compareImages($image2, \Imagick::METRIC_MEANSQUAREERROR);
        return $result[1];
    }

    /**
     * @param $image
     * @return \Imagick
     */
    private function initImage($image): \Imagick
    {
        return $this->imageInitializerService->initImage($image);
    }
}