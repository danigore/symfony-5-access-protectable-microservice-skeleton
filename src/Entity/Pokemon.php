<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PokemonRepository::class)
 */
class Pokemon
{
    public const TYPES = ['normal', 'fire', 'water', 'electric'];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Pokemon::TYPES, message="{{ value }} is not a valid type of Pokemon!")
     */
    private string $Type = 'normal';

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Every pokemon has a name!")
     */
    private $Name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }
}
