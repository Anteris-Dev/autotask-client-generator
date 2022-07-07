<?php

namespace Anteris\Autotask\Generator\Support\Entities;

use Anteris\Autotask\Generator\Helpers\Str;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * This data transfer object represents an entity name. It contains plural and
 * singular forms of the name for use throughout the generator.
 */
class EntityNameDTO extends DataTransferObject
{
    public string $plural;
    public string $pluralSnake;
    public string $singular;
    public string $singularSnake;

    /**
     * Handles snake case stuff.
     */
    public function __construct($parameters)
    {
        if (isset($parameters['plural'])) {
            $parameters['pluralSnake'] = Str::snake($parameters['plural']);
        }

        if (isset($parameters['singular'])) {
            $parameters['singularSnake'] = Str::snake($parameters['singular']);
        }

        parent::__construct($parameters);
    }

    /**
     * Creates a new instance of this class from a string.
     */
    public static function fromString(string $string): EntityNameDTO
    {
        $string = ucfirst($string);

        if (in_array($string, ['ArticlePlainTextContent', 'DocumentPlainTextContent', 'InventoryStockedItemsAdd', 'InventoryStockedItemsRemove', 'InventoryStockedItemsTransfer'])) {
            $array = [
                'plural' => $string,
                'singular' => $string,
            ];
        } else {
            $array = [
                'plural'    => Str::pluralStudly($string),
                'singular'  => Str::singular($string),
            ];
        }

        return new static($array);
    }
}
