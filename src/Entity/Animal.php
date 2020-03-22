<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnimalRepository")
 */
class Animal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Le nom de l'animal doit contenir minimum {{ limit }} caractères",
     *      maxMessage = "Le nom de l'animal doit contenir maximum {{ limit }} caractères"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="integer",  nullable=true)
     * @Assert\Positive
     * @Assert\Length(
     *      min = 1,
     *      max = 2,
     *      minMessage = "L'âge de l'animal doit contenir minimum {{ limit }} chiffre",
     *      maxMessage = "L'âge de l'animal doit contenir maximum {{ limit }} chiffres"
     * )
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identification;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min = 10,
     *      max = 500,
     *      minMessage = "La descripton de l'animal doit contenir minimum {{ limit }} caractères",
     *      maxMessage = "La descripton de l'animal doit contenir maximum {{ limit }} caractères"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sex;

    /**
     * @ORM\Column(type="string", length=255, nullable=true) 
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Le champ doit contenir minimum {{ limit }} caractères",
     *      maxMessage = "Le champ doit contenir maximum {{ limit }} caractères"
     * )
     */
    private $area;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "La ville doit contenir minimum {{ limit }} caractères",
     *      maxMessage = "La ville doit contenir maximum {{ limit }} caractères"
     * )
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="animals")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="animals")
     */
    private $department;

    /**
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="animals")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="animals")
     */
    private $region;

    public function __toString()
    {
        return $this->name;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getIdentification(): ?string
    {
        return $this->identification;
    }

    public function setIdentification(string $identification): self
    {
        $this->identification = $identification;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(string $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }
}
