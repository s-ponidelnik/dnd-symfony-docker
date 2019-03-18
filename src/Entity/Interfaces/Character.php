<?php


namespace App\Entity\Interfaces;


use App\Entity\Collection\HitDiceCollection;

interface Character extends Entity
{
    public function getName(): ?string;

    public function getClasses(): ClassCharacterCollection;

    public function getMaxHP(): int;

    public function getCurrencyHP(): int;

    public function getTemporaryHP(): int;

    public function getExp(): int;

    public function getLevel(): int;

    public function getInventory(): CharacterInventory;

    public function getAttributes(): CharacterAttributeCollection;

    public function getMaxHitDicePull(): HitDiceCollection;

    public function getCurrencyHitDicePull(): HitDiceCollection;
}