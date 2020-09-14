<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * This class is in charge off all client test file generation.
 */
class TestDependencyGenerator
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
     * Creates test dependency classes.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(): void
    {
        // Now build the class
        $this->writer->createAndEnterDirectory('tests');
        $this->writer->createFileFromTemplate(
            'AbstractTest.php',
            'Tests/AbstractTest.php.twig'
        );
    }
}
