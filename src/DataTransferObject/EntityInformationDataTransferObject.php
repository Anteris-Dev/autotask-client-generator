<?php

namespace Anteris\Autotask\Generator\DataTransferObject;

use Spatie\DataTransferObject\DataTransferObject;

class EntityInformationDataTransferObject extends DataTransferObject
{
    public ?bool $canBeCreated;
    public ?bool $canBeDeleted;
    public ?bool $canBeQueried;
    public ?bool $canBeUpdated;
    public ?bool $hasUserDefinedFields;
}
