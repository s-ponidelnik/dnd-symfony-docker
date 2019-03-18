<?php


namespace App\Entity\Interfaces;


use App\Entity\Enum\HitDice;
use App\Entity\Source;

interface CharacterClass extends Entity
{
    public function getIdentifier():string;
    public function getSource():Source;
    public function getSpells():SpellCollection;
    public function getNameEn(): ?string;
    public function getNameRu(): ?string;
    public function getDescriptionEn(): ?string;
    public function getDescriptionRu(): ?string;
    public function getHitDice(): HitDice;


}