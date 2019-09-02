<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IntervalRepository")
 */
class Interval
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $from;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $to;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrom(): ?\DateTimeInterface
    {
        return $this->from;
    }

    public function setFrom(\DateTimeInterface $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?\DateTimeInterface
    {
        return $this->to;
    }

    public function setTo(?\DateTimeInterface $to): self
    {
        $this->to = $to;

        return $this;
    }
}
