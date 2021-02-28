<?php

namespace App\Entity;

use App\Repository\DailyGeneratedCostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DailyGeneratedCostRepository::class)
 */
class DailyGeneratedCost
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=BudgetAdjustmentDate::class, inversedBy="dailyGeneratedCosts")
     */
    private $budgetDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getBudgetDate(): ?BudgetAdjustmentDate
    {
        return $this->budgetDate;
    }

    public function setBudgetDate(?BudgetAdjustmentDate $budgetDate): self
    {
        $this->budgetDate = $budgetDate;

        return $this;
    }
}
