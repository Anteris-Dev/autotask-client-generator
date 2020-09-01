<?php

namespace Anteris\Autotask\Generator\Generators;

use Anteris\Autotask\Generator\Responses\EntityFields\EntityFieldCollection;
use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\TemplateWriter;

/**
 * This class is in charge off all file generation for Entity classes.
 */
class EntityGenerator
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
     * Looks at the types of fields there are and adds any imports needed.
     */
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

    /**
     * Creates a new entity class from the information passed.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(
        EntityNameDTO $entityName,
        EntityInformationDTO $entityInformation,
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
