<?php
/**
 * Created by PhpStorm.
 * User: shinigami
 * Date: 04.01.19
 * Time: 2:55
 */

namespace App\Entity\Enum;


class SpellRangeType extends Enum
{
    const SELF = 0;
    const TOUCH = 1;
    const FT = 2;
    const SPECIAL = 3;
    const CAN_SEE = 4;
    const MILLE = 5;
    const UNLIMITED = 6;
}