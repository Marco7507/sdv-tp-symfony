<?php

namespace App\Application\Reservation;

use App\Entity\Payment;
use App\Entity\User;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class PayReservationUseCase
{
    function __construct(
        private EntityManagerInterface $entityManager,
        private ReservationRepository $reservationRepository,
    )
    {
    }

    function execute(int $reservationId, User $user, string $paymentType): void
    {
        $reservation = $this->reservationRepository->find($reservationId);

        if (!$reservation) {
            throw new \Exception('Reservation not found.');
        }

        if ($reservation->getStatus() !== "CART") {
            throw new \Exception('Payment can only be made for reservations with status CART.');
        }

        if ($reservation->getCreatedBy() !== $user) {
            throw new \Exception('You are not authorized to pay for this reservation.');
        }

        $payment = new Payment($paymentType);

        $reservation->addPayment($payment);

        try {
            $this->entityManager->persist($reservation);
            $this->entityManager->persist($payment);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while processing the payment.');
        }
    }

}