<?php

namespace App\Reservation\PayReservation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PayReservationController extends AbstractController
{
    #[Route('/reservations/{id}/pay', name: 'app_pay_reservation', methods: ['POST'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PayReservationController.php',
        ]);
    }
}
