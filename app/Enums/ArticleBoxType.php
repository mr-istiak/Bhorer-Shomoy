<?php

namespace App\Enums;

enum ArticleBoxType: string
{
    use Enum;

    case TEXT = 'text';
    case IMAGE = 'image';
}
