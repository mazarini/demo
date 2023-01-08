<?php

namespace App\Test\Controller;

use App\Entity\Entity;
use App\Repository\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityRepository $repository;
    private string $value;
    private Entity $entity;
    private string $path = '/entity/';

    protected function setUp(): void
    {
        if (!isset($this->value)) {
            $this->value = md5($this->path);
        }
        $this->client = static::createClient();
        $this->setEntity();
        $this->repository = static::getContainer()->get('doctrine')->getRepository($this->entity::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }

        $this->repository->save($this->entity, true);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Entity index');
        $this->assertSelectorTextContains('body', $this->value);
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'entity[entityLabel]' => 'Testing',
        ]);

        self::assertResponseRedirects('/entity/');
        $this->assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $label = 'My label';
        $fixture = new Entity();
        $fixture->setEntityLabel($label);

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Entity');
        $this->assertSelectorTextContains('body', $label);
    }

    public function testEdit(): void
    {
        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $this->entity->getId()));

        $this->client->submitForm('Update', [
            'entity[entityLabel]' => 'Something New',
        ]);

        self::assertResponseRedirects('/entity/');

        $fixture = $this->repository->find($this->entity->getId());

        self::assertSame('Something New', $fixture->getEntityLabel());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Entity();
        $fixture->setEntityLabel('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/entity/');
    }

    private function setEntity()
    {
        $this->entity = new Entity();
        $this->entity->setEntityLabel($this->value);
    }
}
