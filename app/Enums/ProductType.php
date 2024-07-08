<?php

namespace App\Enums;

enum ProductType: string
{
    case ITEM = 'item';
    case GROUP = 'group';
    case NONSTOCK = 'non-stock';
    case SERVICE = 'service';

    public function description(): string
    {
        return match($this)
        {
            self::ITEM => 'The product item',
            self::GROUP => 'The Group (Multiple items)',
            self::NONSTOCK => 'The product item not has stock counter',
            self::SERVICE => 'The product service',
        };
    }
}
