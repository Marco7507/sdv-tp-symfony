<?php

declare(strict_types=1);

namespace App\Reservation\RemoveInsuranceFromReservation;

use App\Reservation\ReservationNotFoundException;
use App\User\Error\UnauthorizeUserException;
use App\User\User;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveInsuranceFromReservationController extends AbstractController
{
    function __construct(private RemoveInsuranceFromReservationUseCase $removeInsuranceFromReservationUseCase)
    {

    }

    #[Route('/reservations/{id}/remove-insurance', name: "remove_insurance", methods: ['POST'])]
    public function index(Request $request): Response
    {
        $reservationId = (int) $request->query->get('id');

        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(["error" => "Missing credential"], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $this->removeInsuranceFromReservationUseCase->execute($reservationId, $user);

            return $this->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            if ($e instanceof ReservationNotFoundException) {
                return $this->json($e->getMessage(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof UnauthorizeUserException) {
                return $this->json($e->getMessage(), Response::HTTP_FORBIDDEN);
            }

            return $this->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
