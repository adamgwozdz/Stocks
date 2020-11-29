<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistoryRepository::class)
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(name="HIS_VALUE", type="float", precision=10, scale=2)
     */
    private $hisValue;

    /**
     * @ORM\Column(name="HIS_VOLUME", type="integer")
     */
    private $hisVolume;

    /**
     * @ORM\Column(name="HIS_DATE", type="datetime")
     */
    private $hisDate;

    /**
     * @ORM\ManyToOne(targetEntity=Companies::class, inversedBy="companyHistory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHisValue(): ?string
    {
        return $this->hisValue;
    }

    public function setHisValue(string $hisValue): self
    {
        $this->hisValue = $hisValue;

        return $this;
    }

    public function getHisVolume(): ?int
    {
        return $this->hisVolume;
    }

    public function setHisVolume(int $hisVolume): self
    {
        $this->hisVolume = $hisVolume;

        return $this;
    }

    public function getHisDate(): ?\DateTimeInterface
    {
        return $this->hisDate;
    }

    public function setHisDate(\DateTimeInterface $hisDate): self
    {
        $this->hisDate = $hisDate;

        return $this;
    }

    public function getCompany(): ?Companies
    {
        return $this->company;
    }

    public function setCompany(?Companies $company): self
    {
        $this->company = $company;

        return $this;
    }
}
