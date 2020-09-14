<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * This class is in charge off all file generation for QueryBuilder classes.
 */
class QueryBuilderGenerator
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
     * Creates a new query builder class from the information passed.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(EntityNameDTO $entityName): void
    {
        $this->writer->createFileFromTemplate(
            $entityName->singular . 'QueryBuilder.php',
            'Package/API/QueryBuilder.php.twig',
            [
                'entityName' => $entityName,
            ]
        );

        // Calls the paginator dependency
        $paginatorGenerator = new PaginatorGenerator($this->writer);
        $paginatorGenerator->make($entityName);
    }
}
