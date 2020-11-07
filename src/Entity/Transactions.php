<?php

namespace App\Entity;

use App\Repository\TransactionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionsRepository::class)
 */
class Transactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(name="TRN_STOCK", type="string", length=255)
     */
    private $trnStock;

    /**
     * @ORM\Column(name="TRN_AMOUNT", type="integer")
     */
    private $trnAmount;

    /**
     * @ORM\Column(name="TRN_TD", type="datetime", nullable=true)
     */
    private $trnTD;

    /**
     * @ORM\Column(name="TRN_TYPE", type="string", length=20)
     */
    private $trnType;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="userTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrnStock(): ?string
    {
        return $this->trnStock;
    }

    public function setTrnStock(string $trnStock): self
    {
        $this->trnStock = $trnStock;

        return $this;
    }

    public function getTrnAmount(): ?int
    {
        return $this->trnAmount;
    }

    public function setTrnAmount(int $trnAmount): self
    {
        $this->trnAmount = $trnAmount;

        return $this;
    }

    public function getTrnTD(): ?\DateTimeInterface
    {
        return $this->trnTD;
    }

    public function setTrnTD(?\DateTimeInterface $trnTD): self
    {
        $this->trnTD = $trnTD;

        return $this;
    }

    public function getTrnType(): ?string
    {
        return $this->trnType;
    }

    public function setTrnType(string $trnType): self
    {
        $this->trnType = $trnType;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }
}
