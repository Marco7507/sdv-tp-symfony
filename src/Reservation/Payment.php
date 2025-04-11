<?php

namespace App\Reservation;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?PaymentType $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $paidAt = null;

    function __construct(PaymentType $paymentType)
    {
        $this->type = $paymentType;
        $this->paidAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?PaymentType
    {
        return $this->type;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }
}
