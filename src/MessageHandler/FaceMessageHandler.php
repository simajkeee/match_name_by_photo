<?php

namespace App\MessageHandler;

use App\Entity\Task;
use App\Message\FaceMessage;
use App\Models\TaskResponseModel;
use App\Repository\TaskRepository;
use App\Service\FaceMatchingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class FaceMessageHandler implements MessageHandlerInterface
{
    const MAX_ATTEMPTS = 5;
    const SLEEP_TIME = 2;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FaceMatchingService
     */
    private $faceMatchingService;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FaceMatchingService $faceMatchingService,
        SerializerInterface $serializer,
        TaskRepository $taskRepository

    ) {
        $this->entityManager = $entityManager;
        $this->faceMatchingService = $faceMatchingService;
        $this->serializer = $serializer;
        $this->taskRepository = $taskRepository;
    }

    public function __invoke(FaceMessage $faceMessage)
    {
        $task = $this->taskRepository->findOneBy(['id' => $faceMessage->getTaskId()]);

        $retryId = $task->getRetryId();
        if (!$retryId || !($taskModel = $this->getDeserializedRetry($retryId))) {
            return;
        }

        if ($taskModel->getStatus() !== Task::WAIT_STATUS) {
            $this->createTaskFromModelAndSave($taskModel, $task);
            return;
        }
        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            sleep(self::SLEEP_TIME);
            $taskModel = $this->getDeserializedRetry($retryId);
            if ($taskModel->getStatus === Task::SUCCESS_STATUS) {
                break;
            }
        }
        $this->createTaskFromModelAndSave($taskModel, $task);
    }

    private function getDeserializedRetry(string $retryId)
    {
        $retryResult = $this->faceMatchingService->getRetry($retryId);
        return $retryResult ? $this->serializer->deserialize($retryResult, TaskResponseModel::class, 'json') : null;
    }

    private function getTaskFromModelByTask(TaskResponseModel $model, Task $task): Task
    {
        return $task->setStatus($model->getStatus())
                    ->setResult($model->getResult())
                    ->setRetryId($model->getRetryId())
                    ->setErrors($model->getErrors());
    }

    private function createTaskFromModelAndSave(TaskResponseModel $taskModel, Task $task)
    {
        $taskToSave = $this->getTaskFromModelByTask($taskModel, $task);
        $this->taskRepository->save($taskToSave);
    }
}