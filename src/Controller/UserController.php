<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class UserController extends AbstractController
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
            'user' => $user,
        ]);
    }
}
