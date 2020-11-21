<?php

namespace App\Entity;

use App\Repository\CompaniesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompaniesRepository::class)
 */
class Companies
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(name="CPN_NAME", type="string", length=255)
     */
    private $cpnName;

    /**
     * @ORM\Column(name="CPN_COUNTRY", type="string", length=255, nullable=true)
     */
    private $cpnCountry;

    /**
     * @ORM\Column(name="CPN_VOLUME", type="integer")
     */
    private $cpnVolume;

    /**
     * @ORM\Column(name="CPN_MARKET_AREA", type="string", length=255, nullable=true)
     */
    private $cpnMarketArea;

    /**
     * @ORM\Column(name="CPN_CD", type="datetime", nullable=true)
     */
    private $cpnCD;

    /**
     * @ORM\OneToMany(targetEntity=History::class, mappedBy="company", orphanRemoval=true)
     */
    private $companyHistory;

    public function __construct()
    {
        $this->companyHistory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCpnName(): ?string
    {
        return $this->cpnName;
    }

    public function setCpnName(string $cpnName): self
    {
        $this->cpnName = $cpnName;

        return $this;
    }

    public function getCpnCountry(): ?string
    {
        return $this->cpnCountry;
    }

    public function setCpnCountry(?string $cpnCountry): self
    {
        $this->cpnCountry = $cpnCountry;

        return $this;
    }

    public function getCpnVolume(): ?int
    {
        return $this->cpnVolume;
    }

    public function setCpnVolume(int $cpnVolume): self
    {
        $this->cpnVolume = $cpnVolume;

        return $this;
    }

    public function getCpnMarketArea(): ?string
    {
        return $this->cpnMarketArea;
    }

    public function setCpnMarketArea(?string $cpnMarketArea): self
    {
        $this->cpnMarketArea = $cpnMarketArea;

        return $this;
    }

    public function getCpnCD(): ?\DateTimeInterface
    {
        return $this->cpnCD;
    }

    public function setCpnCD(?\DateTimeInterface $cpnCD): self
    {
        $this->cpnCD = $cpnCD;

        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getCompanyHistory(): Collection
    {
        return $this->companyHistory;
    }

    public function addCompanyHistory(History $companyHistory): self
    {
        if (!$this->companyHistory->contains($companyHistory)) {
            $this->companyHistory[] = $companyHistory;
            $companyHistory->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyHistory(History $companyHistory): self
    {
        if ($this->companyHistory->removeElement($companyHistory)) {
            // set the owning side to null (unless already changed)
            if ($companyHistory->getCompany() === $this) {
                $companyHistory->setCompany(null);
            }
        }

        return $this;
    }
}
