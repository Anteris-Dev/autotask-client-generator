<?php

namespace Anteris\Autotask\Generator;

use Anteris\Autotask\Generator\Generators\ClientGenerator;
use Anteris\Autotask\Generator\Generators\ResourceGenerator;
use Anteris\Autotask\Generator\Generators\SupportGenerator;
use Anteris\Autotask\Generator\Responses\EntityFields\EntityFieldCollection;
use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\Entities\EntityNameDTO;
use Anteris\Autotask\Generator\Writers\CacheWriter;
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
    /** @var bool Whether or not the cache should be saved for next run. */
    protected bool $cache;

    /** @var FileWriter Handles the writing of cached responses. */
    protected CacheWriter $cacheWriter;

    /** @var array Keeps track of new classes and returns an existing version if found. */
    protected $classCache = [];

    /** @var HttpClient An HTTP client for gathering information about the resource. */
    protected $client;

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
     * @param  bool    $cache           Whether or not to cache the responses permanently.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(
        string $username,
        string $secret,
        string $integrationCode,
        string $outputDirectory,
        bool $overwrite = false,
        bool $cache = true
    ) {
        // Setup the cache first so we can use it for the Base URL
        $this->cacheWriter = new CacheWriter($outputDirectory);
        $this->cache = $cache;

        // If not caching, do a quick clear of the cache in case something exists.
        if (!$this->cache) {
            $this->cacheWriter->resetCache();
        }

        // Find the base url based on the user
        $cacheKey = "baseurl+$username";

        if (! $this->cacheWriter->inCache($cacheKey)) {
            $recon = (new HttpClient)->get('https://webservices.autotask.net/atservicesrest/v1.0/zoneInformation', [
                'query' => [
                    'user' => $username,
                ]
            ]);

            $response = json_decode($recon->getBody(), true);

            if (!isset($response['url'])) {
                throw new Exception('Invalid base URL!');
            }

            $this->cacheWriter->cache(
                $cacheKey,
                (rtrim($response['url'], '/') . '/v1.0/')
            );
        }

        // Setup our API Client
        $this->client = new HttpClient([
            'base_uri' => $this->cacheWriter->getCached($cacheKey),
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
    }

    /**
     * Makes all the client files required by the API.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeClient(): void
    {
        $clientGenerator = new ClientGenerator($this->templateWriter->newContext());
        $clientGenerator->make();
    }

    /**
     * Make the resource files required by the passed entity.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeResource(string $entityName)
    {
        $resourceGenerator = new ResourceGenerator($this->templateWriter->newContext());
        $entityName = EntityNameDTO::fromString($entityName);

        $resourceGenerator->make(
            $entityName,
            $this->getEntityInformation($entityName),
            $this->getEntityFields($entityName)
        );
    }

    /**
     * Makes all the support files (files used across multiple domains) required by the API.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function makeSupport()
    {
        $supportGenerator = new SupportGenerator($this->templateWriter->newContext());
        $supportGenerator->make();
    }

    /**
     * Retrieves the actions that are allowed by the passed entity.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function getEntityInformation(EntityNameDTO $entityName): EntityInformationDTO
    {
        $key = $entityName->singular . 'EntityInformation';

        if (! $this->cacheWriter->inCache($key)) {
            $this->cacheWriter->cache(
                $key,
                EntityInformationDTO::fromResponse(
                    $this->client->get($entityName->plural . '/entityInformation')
                )
            );
        }

        return $this->cacheWriter->getCached($key);
    }

    /**
     * Retrieves the fields that belong to the passed entity.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    protected function getEntityFields(EntityNameDTO $entityName): EntityFieldCollection
    {
        $key = md5($entityName->singular . 'Fields');

        if (! $this->cacheWriter->inCache($key)) {
            $this->cacheWriter->cache($key,
                EntityFieldCollection::fromResponse(
                    $this->client->get($entityName->plural . '/entityInformation/fields')
                )
            );
        }

        return $this->cacheWriter->getCached($key);
    }

    /**
     * Clears the cache if caching is turned off.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __destruct()
    {
        if ($this->cache == false) {
            $this->cacheWriter->clearCache();
        }
    }
}
