<?php

namespace App\Application\Auth;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class GetTokenUseCase
{
    function __construct(private JWTTokenManagerInterface $jwtTokenManager)
    {

    }

    public function execute(User $user): string
    {
        return $this->jwtTokenManager->create($user);
    }
}