<?php


namespace App\Entity\Interfaces;


interface Source extends Entity
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getOfficial(): ?bool;

    public function getAuthor(): ?string;

    public function getSource(): SourceCollection;

    public function getSpells(): SpellCollection;

    public function getCharacterClasses(): CharacterClassCollection;

    public function __toString(): string;
}