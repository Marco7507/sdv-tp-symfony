<?php

namespace App\Insurance\CreateInsurance;

use App\Insurance\Insurance;
use Doctrine\ORM\EntityManagerInterface;

class CreateInsuranceUseCase
{
    function __construct(private EntityManagerInterface $entityManager)
    {
    }

    function execute(string $name, float $price): Insurance
    {
        $insurance = new Insurance($name, $price);

        try {
            $this->entityManager->persist($insurance);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception('An error occurred while creating the insurance.');
        }

        return $insurance;
    }
}