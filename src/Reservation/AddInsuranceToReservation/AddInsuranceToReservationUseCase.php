<?php

namespace App\Reservation\AddInsuranceToReservation;

use App\Insurance\InsuranceNotFoundException;
use App\Insurance\InsuranceRepository;
use App\Reservation\ReservationNotFoundException;
use App\Reservation\ReservationRepository;
use App\User\User;
use Doctrine\ORM\EntityManagerInterface;

class AddInsuranceToReservationUseCase
{
    function __construct(
        private ReservationRepository  $reservationRepository,
        private EntityManagerInterface $entityManager,
        private InsuranceRepository    $insuranceRepository,
    )
    {
    }

    function execute(int $reservationId, int $insuranceId, User $user): void
    {
        $reservation = $this->reservationRepository->find($reservationId);
        if (!$reservation) {
            throw new ReservationNotFoundException('Reservation not found.');
        }

        $insurance = $this->insuranceRepository->find($insuranceId);
        if (!$insurance) {
            throw new InsuranceNotFoundException('Insurance not found.');
        }

        $reservation->addInsurance($insurance, $user);

        try {
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while adding the insurance to the reservation.');
        }
    }
}