<?php

namespace Anteris\Autotask\Generator\Responses\EntityInformation;

use Exception;
use GuzzleHttp\Psr7\Response;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Represents an entity information response from Autotask.
 */
class EntityInformationDTO extends DataTransferObject
{
    public string $name;
    public bool $canCreate;
    public bool $canDelete;
    public bool $canQuery;
    public bool $canUpdate;
    public string $userAccessForCreate;
    public string $userAccessForDelete;
    public string $userAccessForQuery;
    public string $userAccessForUpdate;
    public bool $hasUserDefinedFields;
    public bool $supportsWebhookCallouts;

    public static function fromResponse(Response $httpResponse): EntityInformationDTO
    {
        $response = json_decode($httpResponse->getBody(), true);

        if (!isset($response['info'])) {
            throw new Exception('Invalid response from entityInformation!');
        }

        return new static($response['info']);
    }
}
