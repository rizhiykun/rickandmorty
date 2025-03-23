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

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    #[ORM\PrePersist]
    public function prePersistCreateAtTrait()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function preUpdateUpdateAtTrait()
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
