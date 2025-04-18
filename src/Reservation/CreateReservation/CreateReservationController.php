<?php

namespace App\Reservation\CreateReservation;

use App\Car\CarNotFoundException;
use App\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreateReservationController extends AbstractController
{
    function __construct(
        private CreateReservationUseCase $createReservationUseCase,
    )
    {
    }

    #[Route('/reservations', name: 'create_reservation', methods: ['POST'])]
    public function createReservation(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $startDateStr = $parameters['startDate'] ?? null;
        $endDateStr = $parameters['endDate'] ?? null;
        $carId = $parameters['carId'] ?? null;

        if (!$startDateStr || !$endDateStr || !$carId) {
            return $this->json([
                'error' => 'Start date, end date, and car ID are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $startDate = \DateTime::createFromFormat('Y-m-d', $startDateStr);
        $endDate = \DateTime::createFromFormat('Y-m-d', $endDateStr);

        if (!$startDate instanceof \DateTime || !$endDate instanceof \DateTime) {
            return $this->json([
                'error' => 'Start date and end date must be valid dates',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'error' => 'User must be logged in to create a reservation',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $reservation = $this->createReservationUseCase->execute($startDate, $endDate, $carId, $user);

            return $this->json($reservation->toJson(), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            if ($e instanceof CarNotFoundException) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            if ($e instanceof \InvalidArgumentException) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
