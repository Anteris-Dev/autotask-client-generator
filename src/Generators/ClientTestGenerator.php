<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Support\ValueObjects\EntityName;
use Anteris\Autotask\Generator\Writers\TemplateWriter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ClientTestGenerator
{
    protected TemplateWriter $writer;

    public function __construct(TemplateWriter $writer)
    {
        $this->writer = $writer;
    }

    public function make(): void
    {
        // This little bit iterates over our output directory
        // to find out which service classes we have there currently.
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->writer->getBaseDir())
        );

        $services = [];

        foreach ($files as $filename => $fileDetails) {
            $filename = pathinfo($filename, PATHINFO_FILENAME);
            if (substr($filename, -strlen('Service')) === 'Service') {
                $name = substr($filename, 0, -strlen('Service'));
                $services[] = EntityName::fromString($name);
            }
        }

        // Now build the class
        $this->writer->createAndEnterDirectory('tests');
        $this->writer->createFileFromTemplate(
            'ClientTest.php',
            'Tests/ClientTest.php.twig',
            [
                'services' => $services,
            ]
        );
    }
}
