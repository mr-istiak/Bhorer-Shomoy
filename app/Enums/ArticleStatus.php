<?php

namespace App\Enums;

enum ArticleStatus: string
{
    use Enum;

    case GENERATED = 'generated';
    case UNDER_REVIEW = 'under_review';
    case EMPTY = 'empty';
}
