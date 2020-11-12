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
class Users implements UserInterface
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
     * @var string The hashed password
     * @ORM\Column(name="USE_PASSWORD", type="string")
     */
    private $password;

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

    /**
     * @ORM\OneToOne(targetEntity=UserMoney::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $userMoney;

    /**
     * @ORM\OneToMany(targetEntity=Wallet::class, mappedBy="user", orphanRemoval=true)
     */
    private $userWallets;

    /**
     * @ORM\OneToMany(targetEntity=Transactions::class, mappedBy="user", orphanRemoval=true)
     */
    private $userTransactions;

    public function __construct()
    {
        $this->userWallets = new ArrayCollection();
        $this->userTransactions = new ArrayCollection();
    }
    

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

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserMoney(): ?UserMoney
    {
        return $this->userMoney;
    }

    public function setUserMoney(UserMoney $userMoney): self
    {
        $this->userMoney = $userMoney;

        // set the owning side of the relation if necessary
        if ($userMoney->getUser() !== $this) {
            $userMoney->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Wallet[]
     */
    public function getUserWallets(): Collection
    {
        return $this->userWallets;
    }

    public function addUserWallet(Wallet $userWallet): self
    {
        if (!$this->userWallets->contains($userWallet)) {
            $this->userWallets[] = $userWallet;
            $userWallet->setUser($this);
        }

        return $this;
    }

    public function removeUserWallet(Wallet $userWallet): self
    {
        if ($this->userWallets->removeElement($userWallet)) {
            // set the owning side to null (unless already changed)
            if ($userWallet->getUser() === $this) {
                $userWallet->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transactions[]
     */
    public function getUserTransactions(): Collection
    {
        return $this->userTransactions;
    }

    public function addUserTransaction(Transactions $userTransaction): self
    {
        if (!$this->userTransactions->contains($userTransaction)) {
            $this->userTransactions[] = $userTransaction;
            $userTransaction->setUser($this);
        }

        return $this;
    }

    public function removeUserTransaction(Transactions $userTransaction): self
    {
        if ($this->userTransactions->removeElement($userTransaction)) {
            // set the owning side to null (unless already changed)
            if ($userTransaction->getUser() === $this) {
                $userTransaction->setUser(null);
            }
        }

        return $this;
    }
}
