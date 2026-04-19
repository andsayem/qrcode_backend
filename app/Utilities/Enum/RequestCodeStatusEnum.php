<?php

namespace App\Utilities\Enum;

/* To get the Keys,
 * Use: StatusEnum::getKeys()
 * To get the Values,
 * Use: StatusEnum::getValues()
 */

abstract class RequestCodeStatusEnum extends BasicEnum
{
    // To call it anywhere, just call: StatusEnum::Active

    const WaitingForApproval = 1;
    const Processing = 2;
    const Success = 3;
    const Rejected = 4;
}

