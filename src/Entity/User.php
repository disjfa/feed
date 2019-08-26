<?php

namespace App\Entity;

use Exception;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements OriginInterface
{
    /**
     * @var Uuid|UuidInterface
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $facebookId;
    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $facebookAccessToken;
    /**
     * @var UserOrigin[]
     * @ORM\OneToMany(targetEntity="UserOrigin", mappedBy="user")
     */
    protected $userOrigins;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    /**
     * @param string|null $facebookId
     */
    public function setFacebookId(?string $facebookId): void
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return string|null
     */
    public function getFacebookAccessToken(): ?string
    {
        return $this->facebookAccessToken;
    }

    /**
     * @param string|null $facebookAccessToken
     */
    public function setFacebookAccessToken(?string $facebookAccessToken): void
    {
        $this->facebookAccessToken = $facebookAccessToken;
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
