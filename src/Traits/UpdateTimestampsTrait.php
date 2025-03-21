<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enum\GroupsType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait UpdateTimestampsTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups([GroupsType::BASE_FIELD])]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups([GroupsType::BASE_FIELD])]
    private $updatedAt;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function prePersistCreateAtTrait()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function preUpdateUpdateAtTrait()
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
