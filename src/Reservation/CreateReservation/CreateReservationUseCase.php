<?php

namespace App\Reservation\CreateReservation;

use App\Car\CarRepository;
use App\Reservation\Reservation;
use App\User\User;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateReservationUseCase
{
    function __construct(
        private EntityManagerInterface $entityManager,
        private CarRepository $carRepository,
    )
    {
    }

    function execute(DateTimeInterface $startDate, DateTimeInterface $endDate, int $carId, User $user): Reservation
    {
        $car = $this->carRepository->find($carId);

        if (!$car) {
            throw new NotFoundHttpException('Car not found.');
        }

        $reservation = new Reservation($startDate, $endDate, $car, $user);

        try {
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while creating the reservation.');
        }

        return $reservation;
    }
}