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
     * @ORM\ManyToOne(targetEntity=CostDate::class, inversedBy="dailyGeneratedCosts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $costDay;

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

    public function getCostDay(): ?CostDate
    {
        return $this->costDay;
    }

    public function setCostDay(?CostDate $costDay): self
    {
        $this->costDay = $costDay;

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
}
