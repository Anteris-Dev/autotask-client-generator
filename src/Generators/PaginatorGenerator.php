<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * This class is in charge off all file generation for paginator classes.
 */
class PaginatorGenerator
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
     * Creates a new paginator class from the information passed.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(EntityNameDTO $entityName): void
    {
        $this->writer->createFileFromTemplate(
            $entityName->singular . 'Paginator.php',
            'Package/API/Paginator.php.twig',
            [
                'entityName' => $entityName,
            ]
        );
    }
}
