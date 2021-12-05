<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Name;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $task = new Task();
        $task->setStatus('wait');
        $task->setTaskNum(1);
        $task->setResult(12.0);

        $image = new Image();
        $image->setImage('test');
        $task->setImage($image);

        $name = new Name();
        $name->setName('testName');
        $task->setName($name);

        $manager->persist($task);
        $manager->flush();
    }
}
