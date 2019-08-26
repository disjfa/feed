<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OriginRepository")
 * @Table(name="origin",uniqueConstraints={@UniqueConstraint(name="item_origin", columns={"origin", "origin_id"})})
 */
class Origin
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $origin;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $originId;
    /**
     * @var UserOrigin[]
     * @ORM\OneToMany(targetEntity="UserOrigin", mappedBy="origin")
     */
    protected $userOrigins;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     */
    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    public function getOriginId(): string
    {
        return $this->originId;
    }

    /**
     * @param string $originId
     */
    public function setOriginId(string $originId): void
    {
        $this->originId = $originId;
    }

    /**
     * @param Origin $other
     *
     * @return bool
     */
    public function equals(Origin $other)
    {
        if ($other->getOrigin() === $this->origin && $other->getOriginId() === $this->originId) {
            return true;
        }

        return false;
    }

    /**
     * @return UserOrigin[]
     */
    public function getUserOrigins(): array
    {
        return $this->userOrigins;
    }

    /**
     * @param UserOrigin[] $userOrigins
     */
    public function setUserOrigins(array $userOrigins): void
    {
        $this->userOrigins = $userOrigins;
    }
}
