<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HolidayRepository")
 */
class Holiday
{

    const PENDING = 'PENDING';
    const APPROVED = 'APPROVED';
    const DENIED = 'DENIED';


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Department", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $department;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Position", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Interval", cascade={"persist"})
     */
    private $intervals;

    /**
     * @ORM\Column(type="text")
     */
    private $status = self::PENDING;

    public function __construct()
    {
        $this->intervals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Interval[]
     */
    public function getIntervals(): Collection
    {
        return $this->intervals;
    }

    public function addInterval(Interval $interval): self
    {
        if (!$this->intervals->contains($interval)) {
            $this->intervals[] = $interval;
        }

        return $this;
    }

    public function removeInterval(Interval $interval): self
    {
        if ($this->intervals->contains($interval)) {
            $this->intervals->removeElement($interval);
        }

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Sets the holiday status to "APPROVED".
     * @return Holiday
     */
    public function approve(): self
    {
        $this->setStatus(self::APPROVED);
        return $this;
    }
    /**
     * Sets the holiday status to "DENIED".
     * @return Holiday
     */
    public function deny(): self
    {
        $this->setStatus(self::DENIED);
        return $this;
    }
}
