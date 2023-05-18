<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Responses\EntityFields\EntityFieldCollection;
use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\ValueObjects\EntityName;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

class EntityGenerator
{
    protected TemplateWriter $writer;

    public function __construct(TemplateWriter $writer)
    {
        $this->writer = $writer;
    }

    protected function getImports(EntityFieldCollection $fields): array
    {
        $imports = [
            'GuzzleHttp\Psr7\Response',
            'Spatie\DataTransferObject\DataTransferObject',
        ];

        foreach ($fields as $field) {
            if ($field->dataType === 'Carbon') {
                $imports[] = 'Carbon\Carbon';
                break;
            }
        }

        return $imports;
    }

    public function make(
        EntityName            $entityName,
        EntityInformationDTO  $entityInformation,
        EntityFieldCollection $fields
    ): void
    {
        $this->writer->createFileFromTemplate(
            $entityName->singular . 'Entity.php',
            'Package/API/Entity.php.twig',
            [
                'entityName' => $entityName,
                'entityInformation' => $entityInformation,
                'fields' => $fields,
                'imports' => $this->getImports($fields),
            ]
        );

        // Make the collection class too
        $collectionGenerator = new CollectionGenerator($this->writer);
        $collectionGenerator->make($entityName);
    }
}
