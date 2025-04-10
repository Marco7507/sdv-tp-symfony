<?php

namespace App\Controller;

use App\Application\Reservation\CreateReservationUseCase;
use App\Application\Reservation\ListReservationsUseCase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    function __construct(
        private ListReservationsUseCase  $listReservationsUseCase,
        private CreateReservationUseCase $createReservationUseCase
    )
    {
    }

    #[Route('/reservations', name: 'app_reservation', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        if ($request->getMethod() === "GET") {
            $reservations = $this->listReservationsUseCase->execute();

            return $this->json($reservations, Response::HTTP_OK);
        }

        if ($request->getMethod() === "POST") {
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

                return $this->json($reservation, Response::HTTP_CREATED);
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $this->json([
            'error' => 'Method not allowed',
        ], Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
