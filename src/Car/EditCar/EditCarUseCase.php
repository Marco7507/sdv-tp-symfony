<?php

namespace App\Car\EditCar;

use App\Car\Car;
use App\Car\CarNotFoundException;
use App\Car\CarRepository;
use App\User\Error\UnauthorizeUserException;
use App\User\User;
use Doctrine\ORM\EntityManagerInterface;

class EditCarUseCase
{
    function __construct(
        private EntityManagerInterface $entityManager,
        private CarRepository          $carRepository
    )
    {
    }

    public function execute(User $user, int $carId, string $model, string $brand, float $pricePerDay) {
        if (!$user->isAdmin()) {
            throw new UnauthorizeUserException('You are not authorized to edit this car');
        }

        $car = $this->carRepository->find($carId);

        if (!$car) {
            throw new CarNotFoundException('Car not found');
        }

        try {
            $car->update($model, $brand, $pricePerDay);
            $this->entityManager->persist($car);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred while editing the car');
        }

        return $car;
    }
}