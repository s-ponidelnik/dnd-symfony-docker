<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SourceRepository")
 */
class Source
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $official;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SpellSchool", mappedBy="source")
     */
    private $source;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Spell", mappedBy="source")
     */
    private $spells;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CharacterClass", mappedBy="source")
     */
    private $characterClasses;

    public function __construct()
    {
        $this->source = new ArrayCollection();
        $this->spells = new ArrayCollection();
        $this->characterClasses = new ArrayCollection();
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

    public function getOfficial(): ?bool
    {
        return $this->official;
    }

    public function setOfficial(bool $official): self
    {
        $this->official = $official;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|SpellSchool[]
     */
    public function getSource(): Collection
    {
        return $this->source;
    }

    public function addSource(SpellSchool $source): self
    {
        if (!$this->source->contains($source)) {
            $this->source[] = $source;
            $source->setSource($this);
        }

        return $this;
    }

    public function removeSource(SpellSchool $source): self
    {
        if ($this->source->contains($source)) {
            $this->source->removeElement($source);
            // set the owning side to null (unless already changed)
            if ($source->getSource() === $this) {
                $source->setSource(null);
            }
        }

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
            $spell->addSource($this);
        }

        return $this;
    }

    public function removeSpell(Spell $spell): self
    {
        if ($this->spells->contains($spell)) {
            $this->spells->removeElement($spell);
            // set the owning side to null (unless already changed)
            if ($spell->getSources() === $this) {
                $spell->removeSource($this);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CharacterClass[]
     */
    public function getCharacterClasses(): Collection
    {
        return $this->characterClasses;
    }

    public function addCharacterClass(CharacterClass $characterClass): self
    {
        if (!$this->characterClasses->contains($characterClass)) {
            $this->characterClasses[] = $characterClass;
            $characterClass->setSource($this);
        }

        return $this;
    }

    public function removeCharacterClass(CharacterClass $characterClass): self
    {
        if ($this->characterClasses->contains($characterClass)) {
            $this->characterClasses->removeElement($characterClass);
            // set the owning side to null (unless already changed)
            if ($characterClass->getSource() === $this) {
                $characterClass->setSource(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
