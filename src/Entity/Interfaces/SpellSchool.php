<?php


namespace App\Entity\Interfaces;


interface SpellSchool extends Entity
{

    public function getId(): ?int;
    public function getIdentifier(): ?string;
    public function getIcon(): ?string;
    public function getSource(): ?Source;
    public function getSpells(): SpellCollection;
    public function getNameEn(): ?string;
    public function getNameRu(): ?string;
    public function getDescriptionEn(): ?string;
    public function getDescriptionRu(): ?string;
}