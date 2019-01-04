<?php
/**
 * Created by PhpStorm.
 * User: shinigami
 * Date: 04.01.19
 * Time: 0:50
 */

namespace App\Entity\Enum;


class SpellCastingTimeType
{
    const SECOND = 1;
    const ACTION = 2;
    const BONUS_ACTION = 3;
    const REACTION = 4;
    const MINUTE = 6;
    const HOUR = 7;
    const ROUND=8;

    public static function getRuDescriptions(): array
    {
        return [
            self::ACTION => 'действие',
            self::BONUS_ACTION => 'бонусное действие',
            self::REACTION => 'реакция',
            self::MINUTE => 'минута',
            self::HOUR => 'час'
        ];
    }

    public static function getByRuDescription(string $description): ?int
    {
        foreach (self::getRuDescriptions() as $key => $ruDescription) {
            if ($description == $ruDescription) {
                return $key;
            }
        }
        return null;
    }
}