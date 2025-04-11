<?php

namespace App\Car\ListCars;

use App\Car\CarRepository;

class ListCarsUseCase
{
    function __construct(private CarRepository $carRepository)
    {
    }

    function execute(): array
    {
        return $this->carRepository->findAll();
    }
}