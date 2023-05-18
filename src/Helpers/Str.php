<?php

namespace Anteris\Autotask\Generator\Helpers;

use Illuminate\Support\Str as StrHelper;

class Str extends StrHelper
{
    private static array $singularFormEqualsPluralForm = [
        'ArticlePlainTextContent',
        'AttachmentInfo',
        'DocumentPlainTextContent',
        'InventoryStockedItemsAdd',
        'InventoryStockedItemsRemove',
        'InventoryStockedItemsTransfer',
        'NotificationHistory',
        'PurchaseOrderItemReceiving',
        'TicketHistory',
    ];

    public static function plural($value, $count = 2): string
    {
        if (in_array($value, self::$singularFormEqualsPluralForm)) {
            return $value;
        }

        return parent::plural($value, $count);
    }

    public static function pluralStudly($value, $count = 2): string
    {
        if (in_array($value, self::$singularFormEqualsPluralForm)) {
            return $value;
        }

        $parts = preg_split('/(.)(?=[A-Z])/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE);

        $lastWord = array_pop($parts);

        return implode('', $parts) . self::plural($lastWord, $count);
    }

    public static function singular($value): string
    {
        if (in_array($value, self::$singularFormEqualsPluralForm)) {
            return $value;
        }

        return parent::singular($value);
    }
}
