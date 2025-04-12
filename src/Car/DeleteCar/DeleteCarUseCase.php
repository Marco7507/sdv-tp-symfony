<?php

namespace App\Car\DeleteCar;

use App\Car\CarNotFoundException;
use App\Car\CarRepository;
use App\User\Error\UnauthorizeUserException;
use App\User\User;
use Doctrine\ORM\EntityManagerInterface;

class DeleteCarUseCase
{
    function __construct(
        private EntityManagerInterface $entityManager,
        private CarRepository $carRepository
    )
    {
    }

    public function execute(User $user, int $carId) {
        if (!$user->isAdmin()) {
            throw new UnauthorizeUserException('You are not authorized to delete this car');
        }

        $car = $this->carRepository->find($carId);

        if (!$car) {
            throw new CarNotFoundException('Car not found');
        }

        try {
            $this->entityManager->remove($car);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred while deleting the car');
        }

        return $car;
    }
}