<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Responses\EntityFields\EntityFieldCollection;
use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * Handles the generation of all resources.
 */
class ResourceGenerator
{
    /** @var TemplateWriter The interface with which we will write new files. */
    protected TemplateWriter $writer;

    /**
     * Sets up the class to begin writing.
     */
    public function __construct(TemplateWriter $templateWriter)
    {
        $this->writer = $templateWriter;
    }

    /**
     * This function actually oversees the making of required files for a specific
     * resource.
     *
     * @param  EntityNameDTO  $entityName  The entity's singular / plural name.
     * @param  EntityInformationDTO  $entityInformation  Information about what actions can be performed against the entity.
     * @param  EntityFieldCollection  $entityFields  A collection of fields the entity has.
     */
    public function make($entityName, $entityInformation, $entityFields)
    {
        $this->writer->createAndEnterDirectory('src/API/' . $entityName->plural);

        $entityGenerator = new EntityGenerator($this->writer);
        $entityGenerator->make($entityName, $entityInformation, $entityFields);

        $serviceGenerator = new ServiceGenerator($this->writer);
        $serviceGenerator->make($entityName, $entityInformation);

        // $this->writer->createAndEnterDirectory('tests/API/' . $entityName->plural);

        // $entityTestGenerator = new EntityTestGenerator($this->fileWriter);
        // $entityTestGenerator->make($entityName, $entityInformation, $entityFields);

        // $serviceTestGenerator = new ServiceTestGenerator($this->fileWriter);
        // $serviceTestGenerator->make($entityName, $entityInformation);
    }
}
