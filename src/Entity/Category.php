<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\EnableAutoMapping]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity('name')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 15,
        maxMessage: 'La catégorie saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères',
    )]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Program::class)]
    private $programs;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    public function __tostring(){
        return $this->name;
    }

    public function __construct()
    {
        $this->programs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrograms(): Collection 
    {
        return $this->programs;
    }
    public function addProgram(Program $program): self
    {
        if (!$this->programs->contains($program)) {
            $this->programs->add($program);
            $program->setCategory($this);
        }

        return $this;
    }
    public function removeProgram(Program $program): self
    {
        if ($this->programs->contains($program)) {
            $this->programs->removeElement($program);
            // Si vous avez besoin de gérer la relation dans l'entité Program
            // Assurez-vous que la méthode `setCategory` dans Program est correcte pour gérer ce changement
            if ($program->getCategory() === $this) {
                $program->setCategory(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
