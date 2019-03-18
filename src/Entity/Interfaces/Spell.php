<?php


namespace App\Entity\Interfaces;


interface Spell extends Entity
{

    public function getId(): ?int;
    public function getIdentifier(): ?string;
    public function getSchool(): ?SpellSchool;
    public function getLevel(): ?int;
    public function getCastingTime(): ?int;
    public function getIsRitual(): ?bool;
    public function getConcentration(): ?bool;
    public function getRangeDistance(): ?int;
    public function getVerbalComponent(): ?bool;
    public function getSomaticComponent(): ?bool;
    public function getMaterialComponents(): ?array;
    public function getNameEn(): ?string;
    public function getNameRu(): ?string;
    public function getDescriptionEn(): ?string;
    public function getDescriptionRu(): ?string;
    public function getCastingTimeType(): ?int;
    public function getCastingTimeDescriptionRu(): ?string;
    public function getCastingTimeDescriptionEn(): ?string;
    public function getDurationType(): ?int;
    public function getDuration(): ?int;
    public function getAreaType(): ?int;
    public function getAreaSize(): ?int;
    public function getRangeType(): ?int;

    public function getAreaSizeType(): ?int;

}