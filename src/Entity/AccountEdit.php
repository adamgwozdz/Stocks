<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class AccountEdit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(name="USE_USERNAME", type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(name="USE_ROLES", type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(name="USE_FIRST_NAME", type="string", length=255)
     */
    private $useFirstName;

    /**
     * @ORM\Column(name="USE_LAST_NAME", type="string", length=255)
     */
    private $useLastName;

    /**
     * @ORM\Column(name="USE_EMAIL", type="string", length=255)
     */
    private $useEmail;

    /**
     * @ORM\Column(name="USE_PHONE", type="string", length=9, nullable=true)
     */
    private $usePhone;



    

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUseFirstName(): ?string
    {
        return $this->useFirstName;
    }

    public function setUseFirstName(string $useFirstName): self
    {
        $this->useFirstName = $useFirstName;

        return $this;
    }

    public function getUseLastName(): ?string
    {
        return $this->useLastName;
    }

    public function setUseLastName(string $useLastName): self
    {
        $this->useLastName = $useLastName;

        return $this;
    }

    public function getUseEmail(): ?string
    {
        return $this->useEmail;
    }

    public function setUseEmail(string $useEmail): self
    {
        $this->useEmail = $useEmail;

        return $this;
    }

    public function getUsePhone(): ?string
    {
        return $this->usePhone;
    }

    public function setUsePhone(?string $usePhone): self
    {
        $this->usePhone = $usePhone;

        return $this;
    }
}
