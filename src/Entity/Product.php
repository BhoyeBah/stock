<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Couleur = null;

    #[ORM\Column]
    private ?int $prixUnit = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\Column]
    private ?int $quantite = 0;

    /**
     * @var Collection<int, Reaprosionner>
     */
    #[ORM\OneToMany(targetEntity: Reaprosionner::class, mappedBy: 'product')]
    private Collection $reaprosionners;

    /**
     * @var Collection<int, OrderDetail>
     */
    #[ORM\OneToMany(targetEntity: OrderDetail::class, mappedBy: 'product')]
    private Collection $orderDetails;

    
    public function __construct()
    {
        $this->quantite = 0;
        $this->reaprosionners = new ArrayCollection();
        $this->orderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->Couleur;
    }

    public function setCouleur(?string $Couleur): static
    {
        $this->Couleur = $Couleur;

        return $this;
    }

    public function getPrixUnit(): ?int
    {
        return $this->prixUnit;
    }

    public function setPrixUnit(int $prixUnit): static
    {
        $this->prixUnit = $prixUnit;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * @return Collection<int, Reaprosionner>
     */
    public function getReaprosionners(): Collection
    {
        return $this->reaprosionners;
    }

    public function addReaprosionner(Reaprosionner $reaprosionner): static
    {
        if (!$this->reaprosionners->contains($reaprosionner)) {
            $this->reaprosionners->add($reaprosionner);
            $reaprosionner->setProduct($this);
        }

        return $this;
    }

    public function removeReaprosionner(Reaprosionner $reaprosionner): static
    {
        if ($this->reaprosionners->removeElement($reaprosionner)) {
            // set the owning side to null (unless already changed)
            if ($reaprosionner->getProduct() === $this) {
                $reaprosionner->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setProduct($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProduct() === $this) {
                $orderDetail->setProduct(null);
            }
        }

        return $this;
    }
}
