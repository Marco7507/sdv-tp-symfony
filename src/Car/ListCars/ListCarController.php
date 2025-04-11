<?php

namespace App\Car\ListCars;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListCarController extends AbstractController
{
    function __construct(
        private ListCarsUseCase $listCarsUseCase
    )
    {
    }

    #[Route('/cars', name: 'cars', methods: ['GET'])]
    public function index(): Response
    {
        $cars = $this->listCarsUseCase->execute();

        return $this->json($cars);
    }
}
