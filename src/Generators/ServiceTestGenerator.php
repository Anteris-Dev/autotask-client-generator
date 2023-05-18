<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\ValueObjects\EntityName;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

class ServiceTestGenerator
{
    protected TemplateWriter $writer;

    public function __construct(TemplateWriter $writer)
    {
        $this->writer = $writer;
    }

    public function make(
        EntityName           $entityName,
        EntityInformationDTO $entityInformation
    ): void
    {
        $this->writer->createAndEnterDirectory('tests/API/' . $entityName->plural);

        $this->writer->createFileFromTemplate(
            $entityName->singular . 'ServiceTest.php',
            'Tests/API/ServiceTest.php.twig',
            [
                'entityName' => $entityName,
                'entityInformation' => $entityInformation,
            ]
        );
    }
}
