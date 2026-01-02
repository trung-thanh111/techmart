<?php

namespace App\Enums;

enum Menu: string
{
    case HEADER = 'header';
    case FOOTER = 'footer';
    case SIDEBAR = 'sidebar';
    case PROFILE = 'profile';
    case MOBILE = 'mobile';

    public static function getPositions(): array
    {
        return [
            self::HEADER->value => 'Header',
            self::FOOTER->value => 'Footer',
            self::SIDEBAR->value => 'Sidebar',
            self::PROFILE->value => 'Profile',
            self::MOBILE->value => 'Mobile',
        ];
    }

    public static function getPositionLabels(): array
    {
        return array_values(self::getPositions());
    }

    public static function getPositionValues(): array
    {
        return array_keys(self::getPositions());
    }
}
