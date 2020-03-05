<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use GuzzleHttp\Psr7\Uri;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 */
class Item
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $guid;
    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $link;
    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $imageUrl;
    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pubDate;
    /**
     * @var Origin[]
     * @ORM\ManyToMany(targetEntity="Origin", cascade={"persist"})
     */
    private $origins;

    /**
     * @var Star[]
     * @ORM\OneToMany(targetEntity="Star", cascade={"persist"}, mappedBy="item")
     */
    private $stars;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->origins = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): void
    {
        $this->guid = $guid;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPubDate(): ?DateTime
    {
        return $this->pubDate;
    }

    public function setPubDate(?DateTime $pubDate): void
    {
        $this->pubDate = $pubDate;
    }

    /**
     * @return ArrayCollection|Origin[]
     */
    public function getOrigins()
    {
        return $this->origins;
    }

    public function addOrigin(Origin $itemOrigin)
    {
        if ($this->origins->contains($itemOrigin)) {
            return;
        }
        foreach ($this->origins as $origin) {
            if ($origin->equals($itemOrigin)) {
                return;
            }
        }

        $this->origins->add($itemOrigin);
    }

    public function removeOrigin(Origin $itemOrigin)
    {
        if ($this->origins->contains($itemOrigin)) {
            $this->origins->removeElement($itemOrigin);
        }
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getImagePath()
    {
        $uri = new Uri($this->imageUrl);

        return $uri->getPath();
    }
}
