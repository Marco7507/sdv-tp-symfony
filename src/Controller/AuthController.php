<?php

namespace App\Controller;

use App\Application\Auth\CreateUserUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    function __construct(private CreateUserUseCase $createUserUseCase)
    {

    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request)
    {
        $parameters = json_decode($request->getContent(), true);
        $email = $parameters['email'] ?? null;
        $password = $parameters['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->createUserUseCase->execute($email, $password);

            return $this->json(['user' => $user], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
