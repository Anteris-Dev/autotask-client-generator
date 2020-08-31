<?php

namespace Anteris\Autotask\Generator\Support\Entities;

use Anteris\Autotask\Generator\Helper\Str;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * This data transfer object represents an entity name. It contains plural and
 * singular forms of the name for use throughout the generator.
 */
class EntityNameDTO extends DataTransferObject
{
    /** @var string The plural form of the endpoint name. */
    public string $plural;

    /** @var string The singular form of the endpoint name. */
    public string $singular;

    /**
     * Creates a new instance of this class from a string.
     */
    public static function fromString(string $string): EntityNameDTO
    {
        $string = ucfirst($string);
        return new static([
            'plural'    => Str::pluralStudly($string),
            'singular'  => Str::singular($string),
        ]);
    }
}
