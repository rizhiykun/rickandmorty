<?php

namespace App\Traits;

use App\Enum\GroupsType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

trait UuidIdTrait
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([GroupsType::BASE_FIELD])]
    private $id;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId($uuid): self
    {
        $this->id = $uuid;

        if (is_string($uuid)) {
            $this->id = UuidV7::fromString($uuid);
        }

        return $this;
    }
}
