<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * This class is in charge off all support file generation.
 */
class SupportGenerator
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
     * Creates all the support classes.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(): void
    {
        // Entity Fields
        $this->writer->createAndEnterDirectory('src/Support/EntityFields');
        $this->writer->createFileFromTemplate(
            'EntityFieldCollection.php',
            'Package/Support/EntityFields/EntityFieldCollection.php.twig'
        );
        $this->writer->createFileFromTemplate(
            'EntityFieldEntity.php',
            'Package/Support/EntityFields/EntityFieldEntity.php.twig'
        );

        // EntityInformation
        $this->writer->resetContext();
        $this->writer->createAndEnterDirectory('src/Support/EntityInformation');
        $this->writer->createFileFromTemplate(
            'EntityInformationEntity.php',
            'Package/Support/EntityInformation/EntityInformationEntity.php.twig'
        );

        // EntityUserDefinedFields
        $this->writer->resetContext();
        $this->writer->createAndEnterDirectory('src/Support/EntityUserDefinedFields');
        $this->writer->createFileFromTemplate(
            'EntityUserDefinedFieldCollection.php',
            'Package/Support/EntityUserDefinedFields/EntityUserDefinedFieldCollection.php.twig'
        );
        $this->writer->createFileFromTemplate(
            'EntityUserDefinedFieldEntity.php',
            'Package/Support/EntityUserDefinedFields/EntityUserDefinedFieldEntity.php.twig'
        );

        // Pagination
        $this->writer->resetContext();
        $this->writer->createAndEnterDirectory('src/Support/Pagination');
        $this->writer->createFileFromTemplate(
            'PageEntity.php',
            'Package/Support/Pagination/PageEntity.php.twig'
        );

        // User Defined Fields
        $this->writer->resetContext();
        $this->writer->createAndEnterDirectory('src/Support/UserDefinedFields');
        $this->writer->createFileFromTemplate(
            'UserDefinedFieldEntity.php',
            'Package/Support/UserDefinedFields/UserDefinedFieldEntity.php.twig'
        );

        // Test Dependencies
        $this->writer->resetContext();
        $this->writer->createAndEnterDirectory('tests');
        $this->writer->createFileFromTemplate(
            'AbstractTest.php',
            'Tests/AbstractTest.php.twig'
        );
    }
}
