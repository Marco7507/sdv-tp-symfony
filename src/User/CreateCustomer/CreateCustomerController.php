<?php

namespace App\User\CreateCustomer;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreateCustomerController extends AbstractController
{
    function __construct(private CreateCustomerUseCase $createCustomerUseCase)
    {
    }

    #[Route('/users', name: 'createUser', methods: ['POST'])]
    public function createUser(Request $request)
    {
        $parameters = json_decode($request->getContent(), true);
        $email = $parameters['email'] ?? null;
        $password = $parameters['password'] ?? null;
        $firstname = $parameters['firstname'] ?? null;
        $lastname = $parameters['lastname'] ?? null;
        $driverLicenseDateStr = $parameters['driverLicenseDate'] ?? null;

        if (!$email || !$password || !$firstname || !$lastname || !$driverLicenseDateStr) {
            return $this->json([
                'error' => 'Email, password, firstname, lastname and driver license date are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $driverLicenseDate = \DateTime::createFromFormat('Y-m-d', $driverLicenseDateStr);

        if (!$driverLicenseDate instanceof \DateTime) {
            return $this->json([
                'error' => 'Driver license date must be a valid date',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->createCustomerUseCase->execute($email, $password, $firstname, $lastname, $driverLicenseDate);

            return $this->json($user, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            if ($e instanceof InvalidArgumentException) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
