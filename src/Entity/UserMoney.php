<?php

namespace App\Entity;

use App\Repository\UserMoneyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserMoneyRepository::class)
 */
class UserMoney
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(name="USM_MONEY", type="float")
     */
    private $usmAmount;

    /**
     * @ORM\OneToOne(targetEntity=Users::class, inversedBy="userMoney", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsmAmount(): ?float
    {
        return $this->usmAmount;
    }

    public function setUsmAmount(float $usmAmount): self
    {
        $this->usmAmount = $usmAmount;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(Users $user): self
    {
        $this->user = $user;

        return $this;
    }
}
