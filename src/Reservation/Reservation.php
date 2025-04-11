<?php

namespace App\Reservation;

use ApiPlatform\Metadata\ApiResource;
use App\Car\Car;
use App\Insurance\Insurance;
use App\User\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?float $totalPrice = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $reservedCar = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne]
    private ?Insurance $insurance = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    function __construct(\DateTimeInterface $startDate, \DateTimeInterface $endDate, Car $car, User $user)
    {
        $this->checkDate($startDate, $endDate);

        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reservedCar = $car;
        $this->createdBy = $user;
        $this->status = "CART";
        $this->totalPrice = $this->calculateTotalPrice();
    }

    private function checkDate(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
    {
        if ($startDate > $endDate) {
            throw new \InvalidArgumentException("La date de début doit être antérieure à la date de fin.");
        }
    }

    private function calculateTotalPrice()
    {
        $totalPrice = 0;

        $interval = $this->startDate->diff($this->endDate);
        $days = $interval->days;

        $totalPrice += $days * $this->reservedCar->getPricePerDay();

        if ($this->insurance) {
            $totalPrice += $this->insurance->getPrice();
        }

        return $totalPrice;
    }

    public function toJson(): array
    {
        $user = $this->getCreatedBy();

        $insurance = $this->getInsurance();

        return [
            'id' => $this->getId(),
            'startDate' => $this->getStartDate(),
            'endDate' => $this->getEndDate(),
            'status' => $this->getStatus(),
            'totalPrice' => $this->getTotalPrice(),
            'reservedCar' => $this->getReservedCar(),
            'createdBy' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
            ],
            'insurance' => $insurance ? [
                'id' => $insurance->getId(),
                'price' => $insurance->getPrice(),
            ] : null,
        ];
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getInsurance(): ?Insurance
    {
        return $this->insurance;
    }

    public function setInsurance(?Insurance $insurance): static
    {
        $this->insurance = $insurance;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getReservedCar(): ?Car
    {
        return $this->reservedCar;
    }

    public function setReservedCar(?Car $reservedCar): static
    {
        $this->reservedCar = $reservedCar;

        return $this;
    }

    public function addInsurance(Insurance $insurance): void
    {
        $this->insurance = $insurance;
        $this->totalPrice = $this->calculateTotalPrice();
    }

    public function removeInsurance(): void
    {
        $this->insurance = null;
        $this->totalPrice = $this->calculateTotalPrice();
    }

    public function addPayment(Payment $payment): void
    {
        $this->payment = $payment;
        $this->status = "PAID";
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        $this->payment = $payment;

        return $this;
    }
}
