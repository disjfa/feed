<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FeedRepository")
 */
class Feed implements OriginInterface
{
    /**
     * @var Uuid|UuidInterface
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $baseUrl;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $link;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title;
    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastBuildDate;
    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pubDate;
    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $indexed;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->indexed = new DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getLastBuildDate(): ?DateTime
    {
        return $this->lastBuildDate;
    }

    public function setLastBuildDate(?DateTime $lastBuildDate): void
    {
        $this->lastBuildDate = $lastBuildDate;
    }

    public function getPubDate(): ?DateTime
    {
        return $this->pubDate;
    }

    public function setPubDate(?DateTime $pubDate): void
    {
        $this->pubDate = $pubDate;
    }

    public function getIndexed(): ?DateTime
    {
        return $this->indexed;
    }

    public function setIndexed(?DateTime $indexed): void
    {
        $this->indexed = $indexed;
    }
}
