<?php

namespace App\Application\Reservation;

use App\Repository\ReservationRepository;

class ListReservationsUseCase
{
    function __construct(
        private ReservationRepository $reservationRepository,
    )
    {
    }

    function execute(): array
    {
        return $this->reservationRepository->findAll();
    }
}