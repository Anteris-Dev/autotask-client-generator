<?php

namespace Anteris\Autotask\Generator;

use Anteris\Autotask\Generator\Generators\ClientGenerator;
use Anteris\Autotask\Generator\Generators\ResourceGenerator;
use Anteris\Autotask\Generator\Generators\SupportGenerator;
use Anteris\Autotask\Generator\Responses\EntityFields\EntityFieldCollection;
use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\FileWriter;
use Anteris\Autotask\Generator\Writers\TemplateWriter;
use Exception;
use GuzzleHttp\Client as HttpClient;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * This class is the parent generator for everything else.
 * 
 * @author Aidan Casey <aidan.casey@anteris.com>
 * @since  v0.1.0
 */
class Generator
{
    /** @var array Keeps track of new classes and returns an existing version if found. */
    protected $classCache = [];

    /** @var HttpClient An HTTP client for gathering information about the resource. */
    protected $client;

    /** @var array Keeps track of new entities and returns an existing version if found. */
    protected $entityCache = [];

    /** @var array Keeps track of new entity fields and returns an existing version if found. */
    protected $fieldCache = [];

    /** @var FileWriter Handles the writing of cached responses. */
    protected FileWriter $cacheWriter;

    /** @var TemplateWriter Handles the actual writing of class files. */
    protected TemplateWriter $templateWriter;

    /** @var Environment An instance of Twig Templating Engine. */
    protected $twig;

    /**
     * Sets up the generator to start creating stuff.
     * 
     * @param  string  $username        The API user's username.
     * @param  string  $secret          The API user's token.
     * @param  string  $outputDirectory The API URL to be used for requests.
     * @param  bool    $overwrite       Whether or not to overwrite existing files.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(
        string $username,
        string $secret,
        string $integrationCode,
        string $outputDirectory,
        bool $overwrite
    ) {
        // Find the base url based on the user
        $recon = (new HttpClient)->get('https://webservices.autotask.net/atservicesrest/v1.0/zoneInformation', [
            'query' => [
                'user' => $username,
            ]
        ]);

        $response = json_decode($recon->getBody(), true);

        if (!isset($response['url'])) {
            throw new Exception('Invalid base URL!');
        }

        $baseUri = rtrim($response['url'], '/') . "/v1.0/";

        // Setup our API Client
        $this->client = new HttpClient([
            'base_uri' => $baseUri,
            'headers'  => [
                'APIIntegrationcode'    => $integrationCode,
                'Username'              => $username,
                'Secret'                => $secret,
                'Content-Type'          => 'application/json',
            ],
            'http_errors' => true,
        ]);

        // Setup our Twig Templating Engine
        $this->twig = new Environment(
            new FilesystemLoader(__DIR__ . '/../templates')
        );

        $this->templateWriter = new TemplateWriter($outputDirectory, $this->twig);
        $this->templateWriter->setOverwrite($overwrite);

        $this->cacheWriter = new FileWriter($outputDirectory);
        $this->cacheWriter->setOverwrite(false);
        $this->cacheWriter->createAndEnterDirectory('.cache');
    }

    /**
     * Makes all the client files required by the API.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeClient(): void
    {
        if (! isset($this->classCache['client'])) {
            $this->classCache['client'] = new ClientGenerator($this->templateWriter);
        }

        $this->templateWriter->resetContext();
        $this->classCache['client']->make();
        $this->templateWriter->resetContext();
    }

    /**
     * Make the resource files required by the passed entity.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeResource(string $entityName)
    {
        if (! isset($this->classCache['resource'])) {
            $this->classCache['resource'] = new ResourceGenerator($this->templateWriter);
        }

        $entityName = EntityNameDTO::fromString($entityName);

        $this->templateWriter->resetContext();
        $this->classCache['resource']->make(
            $entityName,
            $this->getEntityInformation($entityName),
            $this->getEntityFields($entityName)
        );
        $this->templateWriter->resetContext();
    }

    /**
     * Makes all the support files (files used across multiple domains) required by the API.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeSupport()
    {
        if (! isset($this->classCache['support'])) {
            $this->classCache['support'] = new SupportGenerator($this->templateWriter);
        }

        $this->templateWriter->resetContext();
        $this->classCache['support']->make();
        $this->templateWriter->resetContext();
    }

    /**
     * Retrieves the actions that are allowed by the passed entity.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function getEntityInformation(EntityNameDTO $entityName): EntityInformationDTO
    {
        $key = md5($entityName->plural . 'EntityInfo');

        if ($this->cacheWriter->fileExists($key) == false) {
            // Retrieve the field information from Autotask
            $entityInfo = EntityInformationDTO::fromResponse(
                $this->client->get($entityName->plural . '/entityInformation')
            );

            // Write this information to the cache
            $this->cacheWriter->createFile($key, serialize(
                $entityInfo
            ));
        }

        return unserialize($this->cacheWriter->getFile($key));
    }

    /**
     * Retrieves the fields that belong to the passed entity.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    protected function getEntityFields(EntityNameDTO $entityName): EntityFieldCollection
    {
        $key = md5($entityName->plural . 'Fields');

        if ($this->cacheWriter->fileExists($key) == false) {
            // Retrieve the field information from Autotask
            $fields = EntityFieldCollection::fromResponse(
                $this->client->get($entityName->plural . '/entityInformation/fields')
            );

            // Write this information to the cache
            $this->cacheWriter->createFile($key, serialize(
                $fields
            ));
        }

        return unserialize($this->cacheWriter->getFile($key));
    }
}
