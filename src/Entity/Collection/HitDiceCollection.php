<?php


namespace App\Entity\Collection;


use App\Entity\Enum\HitDice;

class HitDiceCollection extends ObjectCollection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(HitDice::class, $elements);
    }
}