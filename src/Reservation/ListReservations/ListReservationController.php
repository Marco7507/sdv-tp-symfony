<?php

namespace App\Reservation\ListReservations;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListReservationController extends AbstractController
{
    function __construct(private ListReservationsUseCase $listReservationsUseCase)
    {
    }

    #[Route('/reservations', name: 'list_reservations', methods: ['GET'])]
    public function listReservations(): Response
    {
        try {
            $reservations = $this->listReservationsUseCase->execute();

            $serializedReservations = array_map(function ($reservation) {
                return $reservation->toJson();
            }, $reservations);

            return $this->json($serializedReservations);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
