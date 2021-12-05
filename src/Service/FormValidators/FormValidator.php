<?php

namespace App\Service\FormValidators;

use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class FormValidator
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
}