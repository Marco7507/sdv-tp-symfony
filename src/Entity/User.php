<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $driverLicenseDate = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'createdBy')]
    private Collection $reservations;

    private function __construct(
        string $email,
        string $password,
        UserPasswordHasherInterface $hasher,
        bool $isAdmin = false,
        string $firstName = null,
        string $lastName = null,
        \DateTimeInterface $driverLicenseDate = null
    )
    {
        $this->checkEmail($email);
        $this->checkPassword($password);

        $hashPassword = $hasher->hashPassword($this, $password);

        $this->email = $email;
        $this->password = $hashPassword;

        $this->roles[] = 'ROLE_USER';
        if ($isAdmin) {
            $this->roles[] = 'ROLE_ADMIN';
        }

        $this->firstname = $firstName;
        $this->lastname = $lastName;
        $this->driverLicenseDate = $driverLicenseDate;
        $this->reservations = new ArrayCollection();
    }

    public static function createCustomer(
        string $email,
        string $password,
        UserPasswordHasherInterface $hasher,
        string $firstName,
        string $lastName,
        \DateTimeInterface $driverLicenseDate
    ): self
    {
        if (empty($firstName) || empty($lastName)) {
            throw new \InvalidArgumentException("Le prénom et le nom de famille ne peuvent pas être vides.");
        }

        if ($driverLicenseDate > new \DateTime()) {
            throw new \InvalidArgumentException("La date de permis de conduire ne peut pas être dans le futur.");
        }

        return new self($email, $password, $hasher, false, $firstName, $lastName, $driverLicenseDate);
    }

    public static function createAdmin(
        string $email,
        string $password,
        UserPasswordHasherInterface $hasher
    ): self
    {
        return new self($email, $password, $hasher, true);
    }

    private function checkEmail(string $email): void
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("L'adresse email est invalide ou vide.");
        }
    }

    private function checkPassword(string $password): void
    {
        if (strlen($password) <= 8) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir plus de 8 caractères.");
        }

        $letters = preg_match_all('/[a-zA-Z]/', $password);
        $digits = preg_match_all('/\d/', $password);

        if ($letters < 4 || $digits < 4) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir au moins 4 lettres et 4 chiffres.");
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->checkEmail($email);

        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->checkPassword($password);

        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDriverLicenseDate(): ?\DateTimeInterface
    {
        return $this->driverLicenseDate;
    }

    public function setDriverLicenseDate(?\DateTimeInterface $driverLicenseDate): static
    {
        $this->driverLicenseDate = $driverLicenseDate;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setCreatedBy($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getCreatedBy() === $this) {
                $reservation->setCreatedBy(null);
            }
        }

        return $this;
    }
}
