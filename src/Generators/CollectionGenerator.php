<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * This class is in charge of all file generation for collection classes.
 */
class CollectionGenerator
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
     * Creates a new collection class from the information passed.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(EntityNameDTO $entityName): void
    {
        $this->writer->createFileFromTemplate(
            $entityName->singular . 'Collection.php',
            'Package/API/Collection.php.twig',
            [
                'entityName' => $entityName,
            ]
        );
    }
}
