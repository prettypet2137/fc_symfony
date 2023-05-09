<?php

namespace App\Entity;

use App\Repository\PlayerTransferRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlayerTransferRepository::class)]
class PlayerTransfer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    private ?Club $club1 = null;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    private ?Club $club2 = null;

    #[ORM\ManyToOne]
    private ?Player $player1 = null;

    #[ORM\ManyToOne]
    private ?Player $player2 = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank]
    private ?int $price = null;

    #[ORM\Column(nullable: false)]
    private ?int $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClub1(): ?Club
    {
        return $this->club1;
    }

    public function setClub1(?Club $club1): self
    {
        $this->club1 = $club1;

        return $this;
    }

    public function getClub2(): ?Club
    {
        return $this->club2;
    }

    public function setClub2(?Club $club2): self
    {
        $this->club2 = $club2;

        return $this;
    }

    public function getPlayer1(): ?Player
    {
        return $this->player1;
    }

    public function setPlayer1(?Player $player1): self
    {
        $this->player1 = $player1;

        return $this;
    }

    public function getPlayer2(): ?Player
    {
        return $this->player2;
    }

    public function setPlayer2(?Player $player2): self
    {
        $this->player2 = $player2;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
