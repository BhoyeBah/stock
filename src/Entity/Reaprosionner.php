<?php

namespace App\Entity;

use App\Repository\ReaprosionnerRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReaprosionnerRepository::class)]
class Reaprosionner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantityAjout = null;

    #[ORM\ManyToOne(inversedBy: 'reaprosionners')]
    private ?Product $product = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;


    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantityAjout(): ?int
    {
        return $this->quantityAjout;
    }

    public function setQuantityAjout(int $quantityAjout): static
    {
        $this->quantityAjout = $quantityAjout;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
