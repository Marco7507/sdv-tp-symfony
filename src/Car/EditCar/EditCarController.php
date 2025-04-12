<?php

declare(strict_types=1);

namespace App\Car\EditCar;

use App\Car\CarNotFoundException;
use App\User\Error\UnauthorizeUserException;
use App\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditCarController extends AbstractController
{
    function __construct(private EditCarUseCase $editCarUseCase)
    {

    }

    #[Route('/cars/{id}', name: 'edit_car', methods: ['PUT'])]
    public function editCar(Request $request): Response
    {
        $carId = (int)$request->attributes->get('id');

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $model = $request->request->get('model');
        $brand = $request->request->get('brand');
        $pricePerDay = (float)$request->request->get('price_per_day');

        if (empty($model) || empty($brand) || $pricePerDay <= 0) {
            return $this->json(['error' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->editCarUseCase->execute($user, $carId, $model, $brand, $pricePerDay);

            return $this->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            if ($e instanceof CarNotFoundException) {
                return $this->json(['error' => 'Car not found'], Response::HTTP_NOT_FOUND);
            }
            if ($e instanceof UnauthorizeUserException) {
                return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            return $this->json(['error' => 'An error occurred while editing the car'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
