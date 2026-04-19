<?php

namespace App\Utilities\Enum;

/* To get the Keys,
 * Use: StatusEnum::getKeys()
 * To get the Values,
 * Use: StatusEnum::getValues()
 */

abstract class SSGCodeStatusEnum extends BasicEnum
{
    // To call it anywhere, just call: StatusEnum::Active

    const Unused = 0;
    const Used = 1;
    const MultipleVerified = 2;
}

