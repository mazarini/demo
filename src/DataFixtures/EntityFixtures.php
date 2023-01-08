<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Entity;

class EntityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0;$i<10;$i++) {
            $manager->persist($this->getEntity($i));
        }
        $manager->flush();
    }

    private function getEntity(int $number): Entity
    {
        $entity = new Entity();
        $entity->setEntityLabel(sprintf('Entity number %d', $number));
        return $entity;
    }
}
