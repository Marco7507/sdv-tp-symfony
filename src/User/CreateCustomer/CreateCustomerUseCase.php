<?php

namespace App\User\CreateCustomer;

use App\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateCustomerUseCase
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $hasher)
    {
    }

    /**
     * @throws \Exception
     */
    public function execute(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        \DateTimeInterface $driverLicenseDate
    ): User
    {
        $customer = User::createCustomer($email, $password, $this->hasher, $firstName, $lastName, $driverLicenseDate);

        try {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while creating the customer.');
        }

        return $customer;
    }
}