<?php

namespace App\Application\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserUseCase
{

    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $hasher)
    {
    }

    /**
     * @throws \Exception
     */
    public function execute(string $email, string $password): User
    {
        $user = new User($email, $password, $this->hasher);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while creating the user.');
        }

        return $user;
    }
}