<?php

namespace App\Application\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class GetCurrentUserUseCase
{
    function __construct(
        private JWTTokenManagerInterface $jwtTokenManager,
        private UserRepository           $userRepository,
    )
    {
    }

    public function execute(string $token): User
    {
        $payload = $this->jwtTokenManager->parse($token);

        if (!isset($payload['id'])) {
            throw new InvalidTokenException();
        }

        $userId = $payload['id'];

        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new InvalidTokenException();
        }

        return $user;
    }
}