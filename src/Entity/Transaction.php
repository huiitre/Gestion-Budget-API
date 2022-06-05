<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $wording;

    /**
     * @ORM\Column(type="float")
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $balance;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $credited_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $debited_at;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $is_fixed;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $is_seen;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get_categories", "get_transactions", "get_users"})
     */
    private $is_active;

    /**
     * ? on vient ajouter le groupe get_transactions pour afficher
     * ? la sous catégorie de la transaction
     * @ORM\ManyToOne(targetEntity=Subcategory::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_transactions", "get_users"})
     */
    private $subcategory;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getCreditedAt(): ?\DateTimeImmutable
    {
        return $this->credited_at;
    }

    public function setCreditedAt(\DateTimeImmutable $credited_at): self
    {
        $this->credited_at = $credited_at;

        return $this;
    }

    public function getDebitedAt(): ?\DateTimeImmutable
    {
        return $this->debited_at;
    }

    public function setDebitedAt(?\DateTimeImmutable $debited_at): self
    {
        $this->debited_at = $debited_at;

        return $this;
    }

    public function isIsFixed(): ?bool
    {
        return $this->is_fixed;
    }

    public function setIsFixed(bool $is_fixed): self
    {
        $this->is_fixed = $is_fixed;

        return $this;
    }

    public function isIsSeen(): ?bool
    {
        return $this->is_seen;
    }

    public function setIsSeen(bool $is_seen): self
    {
        $this->is_seen = $is_seen;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    public function setSubcategory(?Subcategory $subcategory): self
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
