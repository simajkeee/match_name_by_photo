<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task implements \JsonSerializable
{
    const WAIT_STATUS = 'wait';
    const SUCCESS_STATUS = 'success';
    const NOT_FOUND_RESULT_STATUS = 'not_found';
    const READY_STATUS = 'ready';
    const RECEIVED_STATUS = 'received';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $result;

    /**
     * @ORM\ManyToOne(targetEntity=Name::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Image::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $retry_id;

    private $errors;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }


    public function getResult(): ?float
    {
        return $this->result;
    }

    public function setResult(?float $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getName(): ?Name
    {
        return $this->name;
    }

    public function setName(?Name $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public static function fromState(array $state): Task
    {
        $task = new self();

        return $task;
    }

    public function getRetryId(): ?string
    {
        return $this->retry_id;
    }

    public function setRetryId(?string $retry_id): self
    {
        $this->retry_id = $retry_id;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = Name::create($name);

        return $this;
    }

    public function image(string $image): self
    {
        $this->image = Image::create($image);

        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $result = [
            'status' => $this->status,
            'result' => $this->result,
        ];

        if ($this->status === self::WAIT_STATUS) {
            $result['retry_id'] = $this->retry_id;
        }

        if ($this->status === self::RECEIVED_STATUS) {
            $result['task'] = $this->id;
        }

        if ($this->errors) {
            $result['errors'] = $this->errors;
        }

        return $result;
    }
}
