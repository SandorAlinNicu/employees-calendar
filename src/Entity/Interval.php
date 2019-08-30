<?php

namespace App\Entity;

class Interval
{

    private $id;
    private $From;
    private $To;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrom(): ?\DateTimeInterface
    {
        return $this->From;
    }

    public function setFrom(\DateTimeInterface $From): self
    {
        $this->From = $From;

        return $this;
    }

    public function getTo(): ?\DateTimeInterface
    {
        return $this->To;
    }

    public function setTo(?\DateTimeInterface $To): self
    {
        $this->To = $To;

        return $this;
    }
}
