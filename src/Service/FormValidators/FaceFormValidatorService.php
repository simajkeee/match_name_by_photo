<?php

namespace App\Service\FormValidators;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FaceFormValidatorService extends FormValidator
{
    public function validate(array $params)
    {
        return $this->validator->validate($params, $this->getConstraints());
    }

    private function getConstraints()
    {
        return new Collection([
            'name'  => [new Length(['min' => 2]), new NotBlank()],
            'image' => [
                new Image(
                    [
                        'mimeTypes'        => [
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => "This image is not valid.",
                    ]
                ),
                new NotBlank()
            ]
        ]);
    }
}