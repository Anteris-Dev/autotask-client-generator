<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Writers\TemplateWriter;

class TestDependencyGenerator
{
    protected TemplateWriter $writer;

    public function __construct(TemplateWriter $writer)
    {
        $this->writer = $writer;
    }

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
