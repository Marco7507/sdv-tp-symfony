<?php

namespace App\Auth\Login;

use App\User\User;
use Lcobucci\JWT\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class LoginController extends AbstractController
{
    function __construct(
        private LoginUseCase $loginUseCase
    )
    {
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): Response
    {

        try {
            $token = $this->loginUseCase->execute($user);

            return $this->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
