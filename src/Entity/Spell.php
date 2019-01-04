<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpellRepository")
 */
class Spell
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
     * @ORM\ManyToOne(targetEntity="App\Entity\SpellSchool", inversedBy="spells")
     * @ORM\JoinColumn(nullable=false)
     */
    private $school;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $casting_time;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_ritual;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $concentration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $range_distance;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $verbal_component;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $somatic_component;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $material_components = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CharacterClass", inversedBy="spells")
     */
    private $characterClasses;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Source")
     */
    private $sources;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $casting_time_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $casting_time_description_ru;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $casting_time_description_en;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $duration_type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $areaType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $areaSize;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rangeType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $areaSizeType;

    public function __construct()
    {
        $this->characterClasses = new ArrayCollection();
        $this->sources = new ArrayCollection();
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

    public function getSchool(): ?SpellSchool
    {
        return $this->school;
    }

    public function setSchool(?SpellSchool $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getCastingTime(): ?int
    {
        return $this->casting_time;
    }

    public function setCastingTime(int $casting_time): self
    {
        $this->casting_time = $casting_time;

        return $this;
    }

    public function getIsRitual(): ?bool
    {
        return $this->is_ritual;
    }

    public function setIsRitual(bool $is_ritual): self
    {
        $this->is_ritual = $is_ritual;

        return $this;
    }

    public function getConcentration(): ?bool
    {
        return $this->concentration;
    }

    public function setConcentration(bool $concentration): self
    {
        $this->concentration = $concentration;

        return $this;
    }

    public function getRangeDistance(): ?int
    {
        return $this->range_distance;
    }

    public function setRangeDistance(?int $range_distance): self
    {
        $this->range_distance = $range_distance;

        return $this;
    }

    public function getVerbalComponent(): ?bool
    {
        return $this->verbal_component;
    }

    public function setVerbalComponent(bool $verbal_component): self
    {
        $this->verbal_component = $verbal_component;

        return $this;
    }

    public function getSomaticComponent(): ?bool
    {
        return $this->somatic_component;
    }

    public function setSomaticComponent(?bool $somatic_component): self
    {
        $this->somatic_component = $somatic_component;

        return $this;
    }

    public function getMaterialComponents(): ?array
    {
        return $this->material_components;
    }

    public function setMaterialComponents(?array $material_components): self
    {
        $this->material_components = $material_components;

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
        }

        return $this;
    }

    public function removeCharacterClass(CharacterClass $characterClass): self
    {
        if ($this->characterClasses->contains($characterClass)) {
            $this->characterClasses->removeElement($characterClass);
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

    /**
     * @return Collection|Source[]
     */
    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addSource(Source $source): self
    {
        if (!$this->sources->contains($source)) {
            $this->sources[] = $source;
        }

        return $this;
    }

    public function removeSource(Source $source): self
    {
        if ($this->sources->contains($source)) {
            $this->sources->removeElement($source);
        }

        return $this;
    }

    public function getCastingTimeType(): ?int
    {
        return $this->casting_time_type;
    }

    public function setCastingTimeType(int $casting_time_type): self
    {
        $this->casting_time_type = $casting_time_type;

        return $this;
    }

    public function getCastingTimeDescriptionRu(): ?string
    {
        return $this->casting_time_description_ru;
    }

    public function setCastingTimeDescriptionRu(?string $casting_time_description_ru): self
    {
        $this->casting_time_description_ru = $casting_time_description_ru;

        return $this;
    }

    public function getCastingTimeDescriptionEn(): ?string
    {
        return $this->casting_time_description_en;
    }

    public function setCastingTimeDescriptionEn(?string $casting_time_description_en): self
    {
        $this->casting_time_description_en = $casting_time_description_en;

        return $this;
    }

    public function getDurationType(): ?int
    {
        return $this->duration_type;
    }

    public function setDurationType(int $duration_type): self
    {
        $this->duration_type = $duration_type;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getAreaType(): ?int
    {
        return $this->areaType;
    }

    public function setAreaType(?int $areaType): self
    {
        $this->areaType = $areaType;

        return $this;
    }

    public function getAreaSize(): ?int
    {
        return $this->areaSize;
    }

    public function setAreaSize(?int $areaSize): self
    {
        $this->areaSize = $areaSize;

        return $this;
    }

    public function getRangeType(): ?int
    {
        return $this->rangeType;
    }

    public function setRangeType(int $rangeType): self
    {
        $this->rangeType = $rangeType;

        return $this;
    }

    public function getAreaSizeType(): ?int
    {
        return $this->areaSizeType;
    }

    public function setAreaSizeType(?int $areaSizeType): self
    {
        $this->areaSizeType = $areaSizeType;

        return $this;
    }
}
