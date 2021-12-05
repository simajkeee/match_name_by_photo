<?php

namespace App\Models;

use App\Entity\Task;

class TaskResponseModel
{
    private $status;

    private $taskNum;

    private $result;

    private $retryId;

    private $errors;

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int|null
     */
    public function getTaskNum(): ?int
    {
        return $this->taskNum;
    }

    /**
     * @param int|null $taskNum
     */
    public function setTaskNum(?int $taskNum): void
    {
        $this->taskNum = $taskNum;
    }

    /**
     * @return float|string|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param float|string|null $result
     */
    public function setResult($result): void
    {
        if (!isset($result)) {
            return;
        }
        $result = floatval($result);
        if (!is_float($result)) {
            throw new \TypeError("Wrong type of value provided");
        }
        $this->result = floatval($result);
    }

    /**
     * @return mixed
     */
    public function getRetryId()
    {
        return $this->retryId;
    }

    /**
     * @param mixed $retryId
     */
    public function setRetryId($retryId): void
    {
        $this->retryId = $retryId;
    }

    public function createTask(): Task
    {
        return (new Task())
            ->setStatus($this->status)
            ->setResult($this->result)
            ->setErrors($this->errors)
            ->setRetryId($this->retryId);
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors): void
    {
        $this->errors = $errors;
    }
}