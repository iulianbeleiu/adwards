<?php

namespace App\Entity;

use App\Repository\BudgetDailyAdjustmentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BudgetDailyAdjustmentRepository::class)
 */
class BudgetDailyAdjustment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=BudgetAdjustmentDate::class, inversedBy="budgetDailyAdjustments", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $budgetDate;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="float")
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
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
}
