<?php

namespace Anteris\Autotask\Generator\Responses\EntityFields;

use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;

/**
 * Represents a collection of fields.
 */
class EntityFieldCollection extends Collection
{
    /**
     * Create a new collection from a GuzzleResponse.
     */
    public static function fromResponse(Response $httpResponse): EntityFieldCollection
    {
        $response = json_decode($httpResponse->getBody(), true);

        if(! isset($response['fields'])) {
            throw new Exception('Invalid response from entityInformation/fields!');
        }

        return new static(
            EntityFieldDTO::arrayOf($response['fields'])
        );
    }

    /**
     * @inheritdoc
     */
    public function current(): EntityFieldDTO
    {
        return parent::current();
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset): EntityFieldDTO
    {
        return parent::offsetGet($offset);
    }
}
