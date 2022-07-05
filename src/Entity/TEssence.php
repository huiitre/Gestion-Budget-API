<?php

namespace App\Entity;

use App\Repository\TEssenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TEssenceRepository::class)
 */
class TEssence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $km_travelled;

    /**
     * @ORM\Column(type="float")
     */
    private $price_liter;

    /**
     * @ORM\Column(type="float")
     */
    private $tank;

    /**
     * @ORM\ManyToOne(targetEntity=Fuel::class, inversedBy="tEssences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fuel;

    /**
     * @ORM\ManyToOne(targetEntity=Vehicle::class, inversedBy="tEssences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vehicle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKmTravelled(): ?float
    {
        return $this->km_travelled;
    }

    public function setKmTravelled(float $km_travelled): self
    {
        $this->km_travelled = $km_travelled;

        return $this;
    }

    public function getPriceLiter(): ?float
    {
        return $this->price_liter;
    }

    public function setPriceLiter(float $price_liter): self
    {
        $this->price_liter = $price_liter;

        return $this;
    }

    public function getTank(): ?float
    {
        return $this->tank;
    }

    public function setTank(float $tank): self
    {
        $this->tank = $tank;

        return $this;
    }

    public function getFuel(): ?fuel
    {
        return $this->fuel;
    }

    public function setFuel(?fuel $fuel): self
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getVehicle(): ?vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
