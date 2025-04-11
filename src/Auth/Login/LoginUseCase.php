<?php

namespace App\Auth\Login;

use App\User\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class LoginUseCase
{
    function __construct(private JWTTokenManagerInterface $jwtTokenManager)
    {
    }

    public function execute(?User $user): string
    {
        if ($user === null) {
            throw new \Exception('missing credentials');
        }

        return $this->jwtTokenManager->create($user);
    }
}