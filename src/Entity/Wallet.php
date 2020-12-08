<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WalletRepository::class)
 */
class Wallet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @ORM\Column(name="WAL_STOCK", type="string", length=255, nullable=true)
     */
    private $walStock;

    /**
     * @ORM\Column(name="WAL_AMOUNT", type="integer", nullable=true)
     */
    private $walAmount;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="userWallets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="WAL_STOCK_ID", type="integer", nullable=false)
     */
    private $walStockId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getWalStock(): ?string
    {
        return $this->walStock;
    }

    public function setWalStock(?string $walStock): self
    {
        $this->walStock = $walStock;

        return $this;
    }

    public function getWalAmount(): ?int
    {
        return $this->walAmount;
    }

    public function setWalAmount(?int $walAmount): self
    {
        $this->walAmount = $walAmount;

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
    public function setUserId(?int $id): self
    {
        $this->user_id = $id;

        return $this;
    }

    public function getWalStockId(): ?string
    {
        return $this->walStockId;
    }

    public function setWalStockId(?int $walStockId): self
    {
        $this->walStockId = $walStockId;

        return $this;
    }
}
