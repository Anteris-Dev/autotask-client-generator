<?php

namespace Anteris\Autotask\Generator\Helpers;

use Illuminate\Support\Str as StrHelper;

/**
 * Contains helper functions for strings.
 *
 * @author Aidan Casey <aidan.casey@anteris.com>
 */
class Str extends StrHelper
{
    /**
     * Converts a string to its plural form.
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public static function plural($value, $count = 2)
    {
        switch ($value) {
            case 'AttachmentInfo':
            case 'NotificationHistory':
            case 'PurchaseOrderItemReceiving':
            case 'TicketHistory':
                return $value;
                break;
            default:
                return parent::plural($value, $count);
                break;
        }
    }

    /**
     * Pluralize the last word of an English, studly caps case string.
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     */
    public static function pluralStudly($value, $count = 2)
    {
        switch ($value) {
            case 'AttachmentInfo':
            case 'NotificationHistory':
            case 'PurchaseOrderItemReceiving':
            case 'TicketHistory':
                return $value;
                break;
        }

        $parts = preg_split('/(.)(?=[A-Z])/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE);

        $lastWord = array_pop($parts);

        return implode('', $parts) . self::plural($lastWord, $count);
    }

    /**
     * Converts a string to its singular form.
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public static function singular($value, $count = 2)
    {
        switch ($value) {
            case 'AttachmentInfo':
            case 'NotificationHistory':
            case 'PurchaseOrderItemReceiving':
            case 'TicketHistory':
                return $value;
                break;
            default:
                return parent::singular($value, $count);
                break;
        }
    }
}
