<?php
/**
 * Created by PhpStorm.
 * User: shinigami
 * Date: 04.01.19
 * Time: 2:05
 */

namespace App\Entity\Enum;


class SpellDurationType
{
    const SECOND = 1;
    const ACTION = 2;
    const BONUS_ACTION = 3;
    const REACTION = 4;
    const MINUTE = 6;
    const HOUR = 7;
    const INSTANTANEOUS = 8;
    const DAY = 9;
    const UNTIL_DISSIPATES = 10;
    const SPECIAL=11;
    const ROUND=12;
}