<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Support\ValueObjects\EntityName;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

class PaginatorGenerator
{
    protected TemplateWriter $writer;

    public function __construct(TemplateWriter $writer)
    {
        $this->writer = $writer;
    }

    public function make(EntityName $entityName): void
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
