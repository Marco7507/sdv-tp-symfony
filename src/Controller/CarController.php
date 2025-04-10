<?php

namespace App\Controller;

use App\Application\Car\CreateCarUseCase;
use App\Application\Car\ListCarUseCase;
use App\Entity\User;
use InvalidArgumentException;
use Lcobucci\JWT\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class CarController extends AbstractController
{
    function __construct(
        private CreateCarUseCase $createCarUseCase,
        private ListCarUseCase   $listCarUseCase
    )
    {
    }

    #[Route('/cars', name: 'cars', methods: ['POST', 'GET'])]
    public function index(Request $request): Response
    {
        if ($request->getMethod() === "GET") {
            $cars = $this->listCarUseCase->execute();

            return $this->json($cars);
        }

        if ($request->getMethod() === "POST") {
            $parameters = json_decode($request->getContent(), true);
            $model = $parameters['model'] ?? null;
            $brand = $parameters['brand'] ?? null;
            $pricePerDay = $parameters['pricePerDay'] ?? null;

            if (!$model || !$brand || !$pricePerDay) {
                return $this->json([
                    'error' => 'Model, brand, and price per day are required',
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();

            if (!$user instanceof User) {
                return $this->json([
                    'error' => 'User must be logged in to create a car',
                ], Response::HTTP_UNAUTHORIZED);
            }

            try {
                $car = $this->createCarUseCase->execute($user, $model, $brand, $pricePerDay);

                return $this->json($car, Response::HTTP_CREATED);
            } catch (\Exception $e) {
                if ($e instanceof UnauthorizedHttpException) {
                    return $this->json([
                        'error' => $e->getMessage(),
                    ], Response::HTTP_FORBIDDEN);
                }

                if ($e instanceof InvalidArgumentException) {
                    return $this->json([
                        'error' => $e->getMessage(),
                    ], Response::HTTP_BAD_REQUEST);
                }

                return $this->json([
                    'error' => 'An error occurred while creating the car.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $this->json([
            'error' => 'Method not allowed',
        ], Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
