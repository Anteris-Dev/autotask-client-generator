<?php

namespace Anteris\Autotask\Generator;

use Anteris\Autotask\Generator\DataTransferObject\EndpointDataTransferObject;
use Anteris\Autotask\Generator\DataTransferObject\EntityInformationDataTransferObject;
use Anteris\Autotask\Generator\Helper\File;
use Anteris\Autotask\Generator\Helper\Str;
use Exception;
use GuzzleHttp\Client;
use Twig\Environment;

class EndpointGenerator extends AbstractFileWriter
{
    /** @var Client An HTTP client for getting information from Autotask. */
    protected Client $client;

    /** @var EndpointDataTransferObject Stores a singular and plural combo form of the endpoint name.  */
    protected EndpointDataTransferObject $endpoint;

    /** @var array Contains all the field definitions for the Data Transfer Object. */
    protected array $dtoFields    = [];

    /** @var array Contains all the imports required for the Data Transfer Object. */
    protected array $dtoImports   = [];

    /** @var array Contains all the imports required for the Service Object */
    protected $serviceImports = [
        'Spatie\\DataTransferObject\\DataTransferObject',
    ];

    /** @var array If the studdly caps being with these weird words we have to manually map to a lowercase version. */
    protected array $weirdWords = [
        'RMM' => 'rmm',
        'SIC' => 'sic',
    ];

    /**
     * Sets up the class.
     * 
     * @param  Client       $client  An HTTP client for interacting with Autotask.
     * @param  Environment  $twig    Twig engine for using templates.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(Client $client, Environment $twig)
    {
        $this->client = $client;
        parent::__construct($twig);
    }

    /**
     * Sets the endpoint we should interact with.
     * 
     * @param  string  $endpoint  The endpoint we are interacting with.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = new EndpointDataTransferObject([
            'plural'   => Str::pluralStudly($endpoint),
            'singular' => Str::singular($endpoint),
        ]);
    }

    /**
     * Creates all the files related to an endpoint.
     * 
     * @param  bool  $overwrite  Whether or not previous files should be overwritten.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function make(): void
    {
        if (! isset($this->endpoint)) {
            throw new Exception('Please set an endpoint!');
        }

        // Modify write settings
        // $this->setOutputDirectory( $this->outputDirectory );
        $this->setSubDirectory(File::endpointDirectory($this->endpoint));

        // Now write the files
        $this->makeCollection();
        $this->makeDataTransferObject();
        $this->makeService();
    }

    public function makeCollection(): void
    {
        /**
         * Step 1. Check the file
         */
        $filename = File::collectionFilename($this->endpoint);

        if (!$this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Write the information to a file
         */
        $this->writeTemplate(
            'Collection.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
            ]
        ); 
    }

    /**
     * Writes a Data Transfer Object file.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeDataTransferObject(): void
    {
        /**
         * Step 0. Reset stuff
         */
        $this->dtoFields = [];
        $this->dtoImports = [
            'GuzzleHttp\Psr7\Response',
            'Spatie\DataTransferObject\DataTransferObject',
        ];

        /**
         * Step 1. Check the file
         */
        $filename = File::dataTransferObjectFilename($this->endpoint);
        
        if (! $this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Collect information
         */
        $httpResponse   = $this->client->get($this->endpoint->plural . "/entityInformation/fields");
        $arrayResponse  = json_decode($httpResponse->getBody(), true);

        if (!isset($arrayResponse['fields'])) {
            throw new Exception('Invalid response from entityInformation/fields!');
        }

        // Now convert all our fields into something we will understand
        foreach ($arrayResponse['fields'] as $field) {
            // This handles one-off scenarios where the field begins with an all
            // uppercase word (e.g. RMM).
            foreach ($this->weirdWords as $original => $fixed) {
                if (substr($field['name'], 0, strlen($original)) === $original) {
                    $field['name'] = Str::replaceFirst($original, $fixed, $field['name']);
                }
            }

            $this->dtoFields[] = [
                'name'      => Str::camel($field['name']),
                'type'      => $this->mapType($field['dataType']),
                'required'  => $field['isRequired'],
            ];
        }

        /**
         * Step 3. Write the information to a file
         */
        $this->writeTemplate(
            'Entity.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
                'fields'    => $this->dtoFields,
                'imports'   => $this->dtoImports,
            ]
        );
    }

    /**
     * Creates a paginator class for the endpoint.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makePaginator(): void
    {
        /**
         * Step 1. Check the file
         */
        $filename = File::paginatorFilename($this->endpoint);

        if (!$this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Write the information to a file
         */
        $this->writeTemplate(
            'Paginator.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
            ]
        );
    }

    /**
     * Creates a QueryBuilder class for the endpoint.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeQueryBuilder(): void
    {
        /**
         * Step 1. Check the file
         */
        $filename = File::queryBuilderFilename($this->endpoint);

        if (!$this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Write the information to a file
         */
        $this->writeTemplate(
            'QueryBuilder.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
            ]
        );
    }

    /**
     * Writes a service file.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeService()
    {
        /**
         * Step 1. Check the file
         */
        $filename = File::serviceFilename($this->endpoint);

        if (!$this->shouldWriteFile($filename)) {
            return;
        }

        /**
         * Step 2. Collect information
         */
        $httpResponse   = $this->client->get($this->endpoint->plural . "/entityInformation");
        $arrayResponse  = json_decode($httpResponse->getBody(), true);

        if (!isset($arrayResponse['info'])) {
            throw new Exception('Invalid response from entityInformation!');
        }

        $resource = new EntityInformationDataTransferObject([
            'canBeCreated' => $arrayResponse['info']['canCreate'],
            'canBeDeleted' => $arrayResponse['info']['canDelete'],
            'canBeQueried' => $arrayResponse['info']['canQuery'],
            'canBeUpdated' => $arrayResponse['info']['canUpdate'],
            'hasUserDefinedFields' => $arrayResponse['info']['hasUserDefinedFields'],
        ]);

        /**
         * Step 3. Write the information to a file
         */
        $this->writeTemplate(
            'Service.twig',
            $filename,
            [
                'endpoint'  => $this->endpoint,
                'imports'   => $this->serviceImports,
                'resource'  => $resource,
            ]
        );

        /**
         * Step 4. Kick off other generators if applicable.
         */
        if ($resource->canBeQueried) {
            $this->makePaginator();
            $this->makeQueryBuilder();
        }
    }

    /**
     * This helper method converts the Autotask type to a PHP type.
     * 
     * @param  string  $type  The Autotask type to be converted.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    protected function mapType(string $type)
    {
        switch ($type) {
            case 'datetime':
                if (!in_array('Carbon\\Carbon', $this->dtoImports)) {
                    $this->dtoImports[] = 'Carbon\\Carbon';
                }
                return 'Carbon';
                break;
            case 'integer':
                return 'int';
                break;
            case 'long':
            case 'short':
                return 'int';
                break;
            case 'boolean':
                return 'bool';
                break;
            case 'double':
            case 'decimal':
                return 'float';
                break;
            default:
                return $type;
                break;
        }
    }
}
