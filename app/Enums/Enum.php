<?php
namespace App\Enums;

trait Enum
{
    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        // Works for backed enums only
        return array_map(fn($case) => $case->value ?? $case->name, self::cases());
    }
}
