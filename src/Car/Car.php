<?php

namespace App\Car;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ApiResource]
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
        $this->checkModel($model);
        $this->checkBrand($brand);
        $this->checkPricePerDay($pricePerDay);

        $this->model = $model;
        $this->brand = $brand;
        $this->pricePerDay = $pricePerDay;
    }

    public function update(string $model, string $brand, float $pricePerDay)
    {
        $this->checkModel($model);
        $this->checkBrand($brand);
        $this->checkPricePerDay($pricePerDay);

        $this->model = $model;
        $this->brand = $brand;
        $this->pricePerDay = $pricePerDay;
    }

    private function checkModel(string $model): void
    {
        if (empty($model)) {
            throw new InvalidArgumentException("Le modèle de la voiture ne peut pas être vide.");
        }
    }

    private function checkBrand(string $brand): void
    {
        if (empty($brand)) {
            throw new InvalidArgumentException("La marque de la voiture ne peut pas être vide.");
        }
    }

    private function checkPricePerDay(string $pricePerDay): void
    {
        if ($pricePerDay <= 0) {
            throw new InvalidArgumentException("Le prix par jour doit être supérieur à 0.");
        }
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
