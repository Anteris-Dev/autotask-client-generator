<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * This class is in charge of all file generation for service test classes.
 */
class ServiceTestGenerator
{
    /** @var TemplateWriter The interface with which we will write new files. */
    protected $writer;

    /**
     * Sets up the class to begin generating files.
     */
    public function __construct(TemplateWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Creates a new service test class from the information passed.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(
        EntityNameDTO $entityName,
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
