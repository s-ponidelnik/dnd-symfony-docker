<?php

namespace App\Entity;

use App\Entity\Enum\HitDice;
use \App\Entity\Interfaces\CharacterClass as CharacterClassInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CharacterClassRepository")
 */
class CharacterClass implements CharacterClassInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $identifier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Source", inversedBy="characterClasses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $source;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Spell", mappedBy="characterClasses")
     */
    private $spells;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $nameEn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nameRu;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionEn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionRu;

    /**
     * @var string $hitDice
     * @ORM\Column(type="text", nullable=false)
     */
    private $hitDice;

    public function __construct()
    {
        $this->spells = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return Collection|Spell[]
     */
    public function getSpells(): Collection
    {
        return $this->spells;
    }

    public function addSpell(Spell $spell): self
    {
        if (!$this->spells->contains($spell)) {
            $this->spells[] = $spell;
            $spell->addCharacterClass($this);
        }

        return $this;
    }

    public function removeSpell(Spell $spell): self
    {
        if ($this->spells->contains($spell)) {
            $this->spells->removeElement($spell);
            $spell->removeCharacterClass($this);
        }

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): self
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    public function getNameRu(): ?string
    {
        return $this->nameRu;
    }

    public function setNameRu(?string $nameRu): self
    {
        $this->nameRu = $nameRu;

        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(?string $descriptionEn): self
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    public function getDescriptionRu(): ?string
    {
        return $this->descriptionRu;
    }

    public function setDescriptionRu(?string $descriptionRu): self
    {
        $this->descriptionRu = $descriptionRu;

        return $this;
    }

    public function getHitDice(): HitDice
    {
        return HitDice::getInstance($this->hitDice);
    }

    public function setHitDice(HitDice $hitDice): void
    {
        $this->hitDice = $hitDice->getValue();
    }

}
