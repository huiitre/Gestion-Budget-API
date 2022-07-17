<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VehicleRepository::class)
 */
class Vehicle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_vehicle"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_vehicle"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=TEssence::class, mappedBy="vehicle")
     */
    private $tEssences;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_vehicle"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $immat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="vehicles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->tEssences = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, TEssence>
     */
    public function getTEssences(): Collection
    {
        return $this->tEssences;
    }

    public function addTEssence(TEssence $tEssence): self
    {
        if (!$this->tEssences->contains($tEssence)) {
            $this->tEssences[] = $tEssence;
            $tEssence->setVehicle($this);
        }

        return $this;
    }

    public function removeTEssence(TEssence $tEssence): self
    {
        if ($this->tEssences->removeElement($tEssence)) {
            // set the owning side to null (unless already changed)
            if ($tEssence->getVehicle() === $this) {
                $tEssence->setVehicle(null);
            }
        }

        return $this;
    }

    public function getImmat(): ?string
    {
        return $this->immat;
    }

    public function setImmat(string $immat): self
    {
        $this->immat = $immat;

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
