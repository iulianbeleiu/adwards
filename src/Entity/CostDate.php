<?php

namespace App\Entity;

use App\Repository\CostDateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CostDateRepository::class)
 */
class CostDate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $day;

    /**
     * @ORM\OneToMany(targetEntity=DailyGeneratedCost::class, mappedBy="costDay", orphanRemoval=true)
     */
    private $dailyGeneratedCosts;

    public function __construct()
    {
        $this->dailyGeneratedCosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?\DateTimeInterface
    {
        return $this->day;
    }

    public function setDay(\DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return Collection|DailyGeneratedCost[]
     */
    public function getDailyGeneratedCosts(): Collection
    {
        return $this->dailyGeneratedCosts;
    }

    public function addDailyGeneratedCost(DailyGeneratedCost $dailyGeneratedCost): self
    {
        if (!$this->dailyGeneratedCosts->contains($dailyGeneratedCost)) {
            $this->dailyGeneratedCosts[] = $dailyGeneratedCost;
            $dailyGeneratedCost->setCostDay($this);
        }

        return $this;
    }

    public function removeDailyGeneratedCost(DailyGeneratedCost $dailyGeneratedCost): self
    {
        if ($this->dailyGeneratedCosts->removeElement($dailyGeneratedCost)) {
            // set the owning side to null (unless already changed)
            if ($dailyGeneratedCost->getCostDay() === $this) {
                $dailyGeneratedCost->setCostDay(null);
            }
        }

        return $this;
    }
}
