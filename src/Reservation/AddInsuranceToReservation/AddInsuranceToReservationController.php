<?php

namespace App\Reservation\AddInsuranceToReservation;

use App\Insurance\Error\InsuranceNotFoundException;
use App\Reservation\Error\ReservationNotFoundException;
use App\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddInsuranceToReservationController extends AbstractController
{
    function __construct(
        private AddInsuranceToReservationUseCase $addInsuranceUseCase,
    )
    {
    }

    #[Route('/reservations/{id}/add-insurance', name: 'add_insurance_to_reservation', methods: ['PUT'])]
    public function addInsurance(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $insuranceId = $parameters['insuranceId'] ?? null;

        if (!$insuranceId) {
            return $this->json([
                'error' => 'Insurance ID is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $reservationId = (int)$request->attributes->get('id');

        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'error' => 'User must be logged in to add insurance to a reservation',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $this->addInsuranceUseCase->execute($reservationId, $insuranceId, $user);

            return $this->json(['message' => 'Insurance added successfully']);
        } catch (\Exception $e) {
            if ($e instanceof InsuranceNotFoundException || $e instanceof ReservationNotFoundException) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof \InvalidArgumentException) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
