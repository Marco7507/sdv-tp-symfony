<?php

namespace App\Controller;

use App\Application\Auth\CreateUserUseCase;
use App\Application\Auth\GetTokenUseCase;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class AuthController extends AbstractController
{
    function __construct(
        private CreateUserUseCase $createUserUseCase,
        private GetTokenUseCase   $getTokenUseCase
    )
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

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): Response
    {
        if ($user === null) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->getTokenUseCase->execute($user);

        return $this->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
