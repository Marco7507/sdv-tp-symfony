<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InsuranceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: InsuranceRepository::class)]
class Insurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $price = null;

    function __construct(float $price = 20.0)
    {
        if ($price <= 0) {
            throw new \InvalidArgumentException("Le prix de l'assurance doit être supérieur à 0.");
        }

        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
