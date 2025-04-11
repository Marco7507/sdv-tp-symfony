<?php

namespace App\Car\CreateCar;

use App\Car\Car;
use App\User\Error\UnauthorizeUserException;
use App\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateCarUseCase
{
    function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    function execute(User $user, string $model, string $brand, float $pricePerDay): Car
    {
        if (!$user->isAdmin()) {
            throw new UnauthorizeUserException('Only admins can create cars.');
        }

        $car = new Car($model, $brand, $pricePerDay);

        try {
            $this->entityManager->persist($car);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            throw new Exception('An error occurred while creating the car.');
        }

        return $car;
    }
}