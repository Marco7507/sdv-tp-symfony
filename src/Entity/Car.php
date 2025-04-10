<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column]
    private ?float $pricePerDay = null;

    function __construct(string $model, string $brand, float $pricePerDay)
    {
        if (empty($model) || empty($brand)) {
            throw new InvalidArgumentException("Le modèle et la marque de la voiture ne peuvent pas être vides.");
        }
        if ($pricePerDay <= 0) {
            throw new InvalidArgumentException("Le prix par jour doit être supérieur à 0.");
        }

        $this->model = $model;
        $this->brand = $brand;
        $this->pricePerDay = $pricePerDay;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPricePerDay(): ?float
    {
        return $this->pricePerDay;
    }

    public function setPricePerDay(float $pricePerDay): static
    {
        $this->pricePerDay = $pricePerDay;

        return $this;
    }
}
