<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_users"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"get_users", "user"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"get_users"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *      pattern="/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
     *      match=true,
     *      message="Le mot de passe doit contenir au minimum 8 caractères, avec au minimum un chiffre et une lettre majuscule"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_users", "user"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"get_users"})
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Fuel::class, mappedBy="user", orphanRemoval=true)
     */
    private $fuels;

    /**
     * @ORM\OneToMany(targetEntity=Vehicle::class, mappedBy="user", orphanRemoval=true)
     */
    private $vehicles;

    /**
     * @ORM\OneToMany(targetEntity=Todolist::class, mappedBy="user", orphanRemoval=true)
     */
    private $todolists;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->fuels = new ArrayCollection();
        $this->vehicles = new ArrayCollection();
        $this->todolists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
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
        return (string) $this->email;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fuel>
     */
    public function getFuels(): Collection
    {
        return $this->fuels;
    }

    public function addFuel(Fuel $fuel): self
    {
        if (!$this->fuels->contains($fuel)) {
            $this->fuels[] = $fuel;
            $fuel->setUser($this);
        }

        return $this;
    }

    public function removeFuel(Fuel $fuel): self
    {
        if ($this->fuels->removeElement($fuel)) {
            // set the owning side to null (unless already changed)
            if ($fuel->getUser() === $this) {
                $fuel->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): self
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles[] = $vehicle;
            $vehicle->setUser($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): self
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getUser() === $this) {
                $vehicle->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Todolist>
     */
    public function getTodolists(): Collection
    {
        return $this->todolists;
    }

    public function addTodolist(Todolist $todolist): self
    {
        if (!$this->todolists->contains($todolist)) {
            $this->todolists[] = $todolist;
            $todolist->setUser($this);
        }

        return $this;
    }

    public function removeTodolist(Todolist $todolist): self
    {
        if ($this->todolists->removeElement($todolist)) {
            // set the owning side to null (unless already changed)
            if ($todolist->getUser() === $this) {
                $todolist->setUser(null);
            }
        }

        return $this;
    }
}
