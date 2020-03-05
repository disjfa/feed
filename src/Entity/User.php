<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, OriginInterface
{
    const ROLE_DEFAULT = 'ROLE_USER'
    ;
    /**
     * @var Uuid|UuidInterface
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

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
     * User constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->roles = [];
        $this->userOrigins = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if (self::ROLE_DEFAULT === $role) {
            return $this;
        }
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): void
    {
        $this->facebookId = $facebookId;
    }

    public function getFacebookAccessToken(): ?string
    {
        return $this->facebookAccessToken;
    }

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
