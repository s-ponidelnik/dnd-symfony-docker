<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use \App\Entity\Interfaces\SpellSchool as SpellSchoolInterface;
/**
 * @ORM\Entity(repositoryClass="App\Repository\SpellSchoolRepository")
 */
class SpellSchool implements SpellSchoolInterface
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Source", inversedBy="source")
     * @ORM\JoinColumn(nullable=false)
     */
    private $source;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Spell", mappedBy="school")
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

    public function getIcon(): ?string
    {
        if (file_exists(__DIR__ . '/../../public/img/spell/school/' . $this->getIdentifier() . '.png')) {
            return '/img/spell/school/' . $this->getIdentifier() . '.png';// @todo remove hardcore path
        }
        return null;
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
            $spell->setSchool($this);
        }

        return $this;
    }

    public function removeSpell(Spell $spell): self
    {
        if ($this->spells->contains($spell)) {
            $this->spells->removeElement($spell);
            // set the owning side to null (unless already changed)
            if ($spell->getSchool() === $this) {
                $spell->setSchool(null);
            }
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
}
