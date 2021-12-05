<?php

namespace App\Controller;

use App\Constants\ErrorCodes;
use App\Entity\Image;
use App\Entity\Name;
use App\Entity\Task;
use App\Message\FaceMessage;
use App\Models\TaskResponseModel;
use App\Repository\FaceTaskDataRepository;
use App\Service\Facades\FaceFacadeService;
use App\Service\Facades\ImageFacadeService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        MessageBusInterface $bus,
        SerializerInterface $serializer
    ) {
        $this->bus = $bus;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="home", methods={"GET","POST"})
     */
    public function index(
        Request $request,
        ImageFacadeService $imageFacade,
        FaceFacadeService $faceFacade,
        ManagerRegistry $doctrine
    ): Response {
        if ($request->isMethod(Request::METHOD_POST)) {

            $requestName = $request->request->get('name');
            $requestImage = $request->files->get('image');

            $violations = $faceFacade->getFaceFormValidatorService()->validate([
                'name'  => $requestName,
                'image' => $requestImage
            ]);
            if (count($violations) > 0) {
                return $this->json((new Task())->setErrors([ErrorCodes::VIOLATION_CODE => $violations[0]->getMessage()]));
            }

            $imageSignedName = $imageFacade->getImageSignatureService()->getSignatureAndExtension($requestImage);
            $isProcessedTask = $doctrine->getRepository(Task::class)->findByNameAndImage($requestName, $imageSignedName);
            if ($isProcessedTask) {
                if ($isProcessedTask[0]->getStatus() === Task::SUCCESS_STATUS) {
                    $isProcessedTask[0]->setStatus(Task::READY_STATUS);
                }
                return $this->json($isProcessedTask[0]);
            }

            $imagesDirectory = $this->getParameter('images_directory');
            try {
                $imageFacade->getImageUploaderService()->move($imagesDirectory, $requestImage);
            } catch (\Exception $e) {
                return $this->json((new Task())->setErrors([ErrorCodes::EXCEPTION_CODE => $e->getMessage()]));
            }

            $faceMatchingResult = $faceFacade->getFaceMatchingService()->getMatchingResult($imagesDirectory . '/' . $imageSignedName, $requestName);
            if (!$faceMatchingResult) {
                return $this->json((new Task())->setErrors([ErrorCodes::API_NO_RESULT_CODE => "Unable to retrieve face matching result"]));
            }
            $taskModel = $this->serializer->deserialize($faceMatchingResult, TaskResponseModel::class, 'json');
            //Uncomment lines under to retrieve task in status "wait"
//            while ($taskModel->getStatus() !== Task::WAIT_STATUS) {
//                $faceMatchingResult = $faceFacade->getFaceMatchingService()->getMatchingResult($imagesDirectory . '/' . $imageSignedName, $requestName . rand(0, PHP_INT_MAX));
//                $taskModel = $this->serializer->deserialize($faceMatchingResult, TaskResponseModel::class, 'json');
//            }
            $newTask = $taskModel->createTask();
            $newTask = ($foundName = $doctrine->getRepository(Name::class)->findOneBy(['name' => $requestName])) ? $newTask->setName($foundName) : $newTask->name($requestName);
            $newTask = ($foundImage = $doctrine->getRepository(Image::class)->findOneBy(['image' => $imageSignedName])) ? $newTask->setImage($foundImage) : $newTask->image($imageSignedName);
            $doctrine->getRepository(Task::class)->save($newTask);

            if ($newTask->getStatus() === Task::WAIT_STATUS) {
                $this->bus->dispatch(new FaceMessage($newTask->getId()));
            }
            return $this->json($newTask->setStatus(Task::RECEIVED_STATUS));
        } else if ($request->isMethod(Request::METHOD_GET) && $request->get('task_id')) {
            $result = $doctrine->getRepository(Task::class)->findOneBy(['id' => $request->get('task_id')]);
            if (!$result) {
                $result = (new Task())->setStatus(Task::NOT_FOUND_RESULT_STATUS);
            }
            return $this->json($result);
        }
        return $this->render('home/index.html.twig');
    }
}
