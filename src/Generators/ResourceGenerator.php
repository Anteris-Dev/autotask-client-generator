<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Responses\EntityFields\EntityFieldCollection;
use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\ValueObjects\EntityName;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

class ResourceGenerator
{
    protected TemplateWriter $writer;

    public function __construct(TemplateWriter $templateWriter)
    {
        $this->writer = $templateWriter;
    }

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
