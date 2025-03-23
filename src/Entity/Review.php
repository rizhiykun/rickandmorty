<?php

namespace App\Entity;

use App\Enum\GroupsType;
use App\Repository\ReviewRepository;
use App\Traits\UpdateTimestampsTrait;
use App\Traits\UuidIdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Review
{
    use UuidIdTrait, UpdateTimestampsTrait;

    #[ORM\Column(type: Types::INTEGER, length: 25, nullable: true)]
    #[Groups([GroupsType::REVIEW])]
    private ?int $episodeId = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups([GroupsType::REVIEW])]
    private ?string $reviewText = null;

    #[ORM\Column(type: Types::FLOAT, length: 25, nullable: true)]
    #[Groups([GroupsType::REVIEW])]
    private ?float $sentimentScore = null;

    public function getEpisodeId(): ?int
    {
        return $this->episodeId;
    }

    public function setEpisodeId(int $episodeId): static
    {
        $this->episodeId = $episodeId;

        return $this;
    }

    public function getReviewText(): ?string
    {
        return $this->reviewText;
    }

    public function setReviewText(?string $reviewText): static
    {
        $this->reviewText = $reviewText;

        return $this;
    }

    public function getSentimentScore(): ?float
    {
        return $this->sentimentScore;
    }

    public function setSentimentScore(float $sentimentScore): static
    {
        $this->sentimentScore = $sentimentScore;

        return $this;
    }
}
