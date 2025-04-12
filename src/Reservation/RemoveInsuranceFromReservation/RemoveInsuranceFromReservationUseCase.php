<?php

namespace App\Reservation\RemoveInsuranceFromReservation;

use App\Reservation\ReservationNotFoundException;
use App\Reservation\ReservationRepository;
use App\User\Error\UnauthorizeUserException;
use App\User\User;
use Doctrine\ORM\EntityManagerInterface;

class RemoveInsuranceFromReservationUseCase
{
    function __construct(
        private EntityManagerInterface $entityManager,
        private ReservationRepository $reservationRepository
    )
    {
    }

    public function execute(int $reservationId, User $user) {
        $reservation = $this->reservationRepository->find($reservationId);

        if (!$reservation) {
            throw new ReservationNotFoundException('Reservation not found');
        }

        if ($reservation->getUser() !== $user) {
            throw new UnauthorizeUserException('You are not allowed to remove insurance from this reservation');
        }

        $reservation->removeInsurance();

        try {
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred while removing insurance from the reservation');
        }

        return $reservation;
    }
}