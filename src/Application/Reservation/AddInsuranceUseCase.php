<?php

namespace App\Application\Reservation;

use App\Entity\Insurance;
use App\Entity\User;
use App\Repository\InsuranceRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddInsuranceUseCase
{
    function __construct(
        private ReservationRepository  $reservationRepository,
        private EntityManagerInterface $entityManager,
        private InsuranceRepository $insuranceRepository,
    )
    {
    }

    function execute(int $reservationId, int $insuranceId, User $user): void
    {
        $reservation = $this->reservationRepository->find($reservationId);
        if (!$reservation) {
            throw new \Exception('Reservation not found.');
        }

        if ($reservation->getStatus() !== "CART") {
            throw new \Exception('Insurance can only be added to reservations with status CART.');
        }

        if ($reservation->getCreatedBy() !== $user) {
            throw new \Exception('You are not authorized to add insurance to this reservation.');
        }

        $insurance = $this->insuranceRepository->find($insuranceId);
        if (!$insurance) {
            throw new \Exception('Insurance not found.');
        }

        $reservation->addInsurance($insurance);

        try {
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while adding the insurance to the reservation.');
        }
    }
}