<?php

namespace Anteris\Autotask\Generator\DataTransferObject;

use Spatie\DataTransferObject\DataTransferObject;

class EndpointDataTransferObject extends DataTransferObject
{
    /** @var string The plural form of the endpoint name. */
    public string $plural;

    /** @var string The singular form of the endpoint name. */
    public string $singular;
}
