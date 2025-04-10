<?php

namespace App\Application\Car;

use App\Entity\Car;
use App\Repository\CarRepository;

class ListCarUseCase
{
    function __construct(private CarRepository $carRepository)
    {
    }

    function execute(): array
    {
        return $this->carRepository->findAll();
    }
}