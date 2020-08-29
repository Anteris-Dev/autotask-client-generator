<?php

namespace Anteris\Autotask\Generator\Responses\EntityFields;

use Anteris\Autotask\Generator\Helper\Str;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Represents an entity field response from Autotask.
 */
class EntityFieldDTO extends DataTransferObject
{
    public string $name;
    public string $dataType;
    public int $length;
    public bool $isRequired;
    public bool $isReadOnly;
    public bool $isQueryable;
    public bool $isReference;
    public string $referenceEntityType;
    public bool $isPickList;
    public ?array $picklistValues;
    public ?string $picklistParentValueField;
    public bool $isSupportedWebhookField;

    /**
     * Overrides the default construct to fix some problems.
     */
    public function __construct(array $parameters = [])
    {
        // Convert Autotask types to PHP types
        if (isset($parameters['dataType'])) {
            switch ($parameters['dataType']) {
                case 'datetime':
                    $parameters['dataType'] = 'Carbon';
                    break;
                case 'integer':
                    $parameters['dataType'] = 'int';
                    break;
                case 'long':
                case 'short':
                    $parameters['dataType'] = 'int';
                    break;
                case 'boolean':
                    $parameters['dataType'] = 'bool';
                    break;
                case 'double':
                case 'decimal':
                    $parameters['dataType'] = 'float';
                    break;
            }
        }

        // This is a terrible block of code to deal with weird camel cased words
        if (isset($parameters['name'])) {
            $weirdWords = [
                'RMM' => 'rmm',
                'SIC' => 'sic',
            ];

            foreach ($weirdWords as $original => $fixed) {
                if (substr($parameters['name'], 0, strlen($original)) !== $original) {
                    continue;
                }

                $parameters['name'] = Str::replaceFirst(
                    $original,
                    $fixed,
                    $parameters['name']
                );
            }

            $parameters['name'] = Str::camel($parameters['name']);
        }

        parent::__construct($parameters);
    }
}
