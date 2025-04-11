<?php

namespace App\User\GetCurrentUser;

use App\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetCurrentUserController extends AbstractController
{
    #[Route('/users/me', name: 'app_user')]
    public function getMe(#[CurrentUser] ?User $user): Response
    {
        $this->getUser();

        if ($user === null) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ])->setStatusCode(Response::HTTP_OK);
    }
}
