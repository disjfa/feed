<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StarRepository")
 * @ORM\Table(
 *     uniqueConstraints={@UniqueConstraint(name="user_item", columns={"item_id", "user_id"})},
 *     indexes={@Index(name="user_item_index", columns={"item_id", "user_id"})}
 * )
 */
class Star
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var Item
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="stars")
     */
    private $item;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @param Item $item
     * @param User $user
     * @throws Exception
     */
    public function __construct(Item $item, User $user)
    {
        $this->id = Uuid::uuid4();
        $this->item = $item;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
