<?php
namespace App\Enums;

enum PostStatus: string
{
    use Enum;

    case PUBLISHED = 'published';
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case DRAFT = 'draft';
}
