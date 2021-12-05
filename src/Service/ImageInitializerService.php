<?php

namespace App\Service;

class ImageInitializerService
{
    public function initImage($image)
    {
        if (is_string($image)) {
            return new \Imagick($image);
        } elseif ($image instanceof \Imagick) {
            return $image;
        } else {
            throw new Exception('Image input must be Imagick object or string path (' . gettype($image) . ' sent)');
        }
    }
}