<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textContent = null;

    #[ORM\Column]
    private ?int $ownerId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10, nullable: true)]
    private ?string $longitude = null;

    #[ORM\OneToOne(targetEntity: MediaFile::class)]
    #[ORM\JoinColumn(name: "mediafile_id", referencedColumnName: "id")]
    private $mediaFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    public function setTextContent(?string $textContent): self
    {
        $this->textContent = $textContent;

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(int $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getMediaFile(): ?MediaFile
    {
        return $this->mediaFile;
    }

    public function setMediaFile(?MediaFile $mediaFile): self
    {
        $this->mediaFile = $mediaFile;

        return $this;
    }
}
