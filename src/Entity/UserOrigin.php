<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserOriginRepository")
 * @Table(name="user_origin",uniqueConstraints={@UniqueConstraint(name="user_user_origin", columns={"user_id", "origin_id"})})
 */
class UserOrigin
{
    /**
     * @var Uuid|UuidInterface
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userOrigins")
     */
    private $user;
    /**
     * @var Origin
     * @ORM\ManyToOne(targetEntity="Origin", inversedBy="userOrigins")
     */
    private $origin;

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
    public function getId()
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getOrigin(): Origin
    {
        return $this->origin;
    }

    public function setOrigin(Origin $origin): void
    {
        $this->origin = $origin;
    }
}
