<?php


namespace App\Entity\Interfaces;


interface ClassCharacter extends Entity
{
    public function getCharacter():Character;
    public function getLevel():int;
    public function getClass():CharacterClass;
}