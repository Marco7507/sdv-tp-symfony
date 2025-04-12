<?php

declare(strict_types=1);

namespace App\Car\DeleteCar;

use App\Car\CarNotFoundException;
use App\User\Error\UnauthorizeUserException;
use App\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteCarController extends AbstractController
{
    function __construct(private DeleteCarUseCase $deleteCarUseCase)
    {
    }

    #[Route('/cars/{id}', name: 'delete_car', methods: ['DELETE'])]
    public function deleteCar(Request $request): Response
    {
        $carId = (int)$request->attributes->get('id');

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $this->deleteCarUseCase->execute($user, $carId);

            return $this->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            if ($e instanceof CarNotFoundException) {
                return $this->json(['error' => 'Car not found'], Response::HTTP_NOT_FOUND);
            }
            if ($e instanceof UnauthorizeUserException) {
                return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            return $this->json(['error' => 'An error occurred while deleting the car'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
