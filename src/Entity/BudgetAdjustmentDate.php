<?php

namespace App\Entity;

use App\Repository\BudgetAdjustmentDateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BudgetAdjustmentDateRepository::class)
 */
class BudgetAdjustmentDate
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
     * @ORM\OneToMany(targetEntity=BudgetDailyAdjustment::class, mappedBy="budgetDate", orphanRemoval=true)
     */
    private $budgetDailyAdjustments;

    /**
     * @ORM\OneToMany(targetEntity=DailyGeneratedCost::class, mappedBy="budgetDate")
     */
    private $dailyGeneratedCosts;

    public function __construct()
    {
        $this->budgetDailyAdjustments = new ArrayCollection();
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
     * @return Collection|BudgetDailyAdjustment[]
     */
    public function getBudgetDailyAdjustments(): Collection
    {
        return $this->budgetDailyAdjustments;
    }

    public function addBudgetDailyAdjustment(BudgetDailyAdjustment $budgetDailyAdjustment): self
    {
        if (!$this->budgetDailyAdjustments->contains($budgetDailyAdjustment)) {
            $this->budgetDailyAdjustments[] = $budgetDailyAdjustment;
            $budgetDailyAdjustment->setBudgetDate($this);
        }

        return $this;
    }

    public function removeBudgetDailyAdjustment(BudgetDailyAdjustment $budgetDailyAdjustment): self
    {
        if ($this->budgetDailyAdjustments->removeElement($budgetDailyAdjustment)) {
            // set the owning side to null (unless already changed)
            if ($budgetDailyAdjustment->getBudgetDate() === $this) {
                $budgetDailyAdjustment->setBudgetDate(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return strval($this->id);
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
            $dailyGeneratedCost->setBudgetDate($this);
        }

        return $this;
    }

    public function removeDailyGeneratedCost(DailyGeneratedCost $dailyGeneratedCost): self
    {
        if ($this->dailyGeneratedCosts->removeElement($dailyGeneratedCost)) {
            // set the owning side to null (unless already changed)
            if ($dailyGeneratedCost->getBudgetDate() === $this) {
                $dailyGeneratedCost->setBudgetDate(null);
            }
        }

        return $this;
    }
}
