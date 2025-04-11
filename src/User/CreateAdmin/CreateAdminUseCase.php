<?php

namespace App\User\CreateAdmin;

use App\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminUseCase
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
        $admin = User::createAdmin($email, $password, $this->hasher);

        try {
            $this->entityManager->persist($admin);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while creating the admin.');
        }

        return $admin;
    }
}