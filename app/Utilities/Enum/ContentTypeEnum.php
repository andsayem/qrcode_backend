<?php

namespace App\Utilities\Enum;

abstract class ContentTypeEnum extends BasicEnum
{
    // To call it anywhere, just call: StatusEnum::Active

    const Image = 'image';
    const Link = 'link';
}
