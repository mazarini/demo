<?php

namespace App\Entity;

use App\Repository\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntityRepository::class)]
class Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 31)]
    private string $entityLabel = '';

    public function getId(): int
    {
        if ($this->id === null) {
            return 0;
        }
        return $this->id;
    }

    public function getEntityLabel(): ?string
    {
        return $this->entityLabel;
    }

    public function setEntityLabel(string $entityLabel): self
    {
        $this->entityLabel = $entityLabel;

        return $this;
    }
}
