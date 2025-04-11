<?php

namespace App\Reservation\ListReservations;

use App\Reservation\ReservationRepository;

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