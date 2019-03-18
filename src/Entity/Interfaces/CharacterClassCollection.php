<?php


namespace App\Entity\Interfaces;


use App\Entity\Collection\Interfaces\ObjectCollection;

interface CharacterClassCollection extends ObjectCollection
{
    public function add(CharacterClass $element): void;
}