<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Support\ValueObjects\EntityName;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

class QueryBuilderGenerator
{
    protected TemplateWriter $writer;

    public function __construct(TemplateWriter $writer)
    {
        $this->writer = $writer;
    }

    public function make(EntityName $entityName): void
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
