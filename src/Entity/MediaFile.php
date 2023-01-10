<?php

namespace App\Entity;

use App\Repository\MediaFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaFileRepository::class)]
class MediaFile
{
    private const DEFAULT_LIKES = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\Column(length: 255)]
    private ?string $file_url = null;

    #[ORM\Column]
    private ?int $file_likes = self::DEFAULT_LIKES;

    #[ORM\Column]
    private ?int $file_owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->file_url;
    }

    public function setFileUrl(string $file_url): self
    {
        $this->file_url = $file_url;

        return $this;
    }

    public function getFileLikes(): ?int
    {
        return $this->file_likes;
    }

    public function setFileLikes(int $file_likes): self
    {
        $this->file_likes = $file_likes;

        return $this;
    }

    public function getFileOwner(): ?int
    {
        return $this->file_owner;
    }

    public function setFileOwner(int $file_owner): self
    {
        $this->file_owner = $file_owner;

        return $this;
    }
}
