<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\TemplateWriter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * This class is in charge off all client file generation.
 */
class ClientGenerator
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
     * Creates all the client classes.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
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
                $services[] = EntityNameDTO::fromString($name);
            }
        }

        // Now build the class
        $this->writer->createAndEnterDirectory('src');
        $this->writer->createFileFromTemplate(
            'Client.php',
            'Package/Client.php.twig',
            [
                'services' => $services,
            ]
        );

        $this->writer->createFileFromTemplate(
            'HttpClientFactory.php',
            'Package/HttpClientFactory.php.twig'
        );
    }
}
