<?php

namespace Anteris\Autotask\Generator;

use Anteris\Autotask\Generator\Generators\ClientGenerator;
use Anteris\Autotask\Generator\Generators\ResourceGenerator;
use Anteris\Autotask\Generator\Generators\SupportGenerator;
use Anteris\Autotask\Generator\Responses\EntityFields\EntityFieldCollection;
use Anteris\Autotask\Generator\Responses\EntityInformation\EntityInformationDTO;
use Anteris\Autotask\Generator\Support\ValueObjects\EntityName;
use Anteris\Autotask\Generator\Writers\CacheWriter;
use Anteris\Autotask\Generator\Writers\TemplateWriter;
use Exception;
use GuzzleHttp\Client as HttpClient;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Generator
{
    protected bool $cache;

    protected CacheWriter $cacheWriter;

    protected array $classCache = [];

    protected HttpClient $client;

    protected TemplateWriter $templateWriter;

    protected Environment $twig;

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

    public function makeClient(): void
    {
        $clientGenerator = new ClientGenerator($this->templateWriter->newContext());
        $clientGenerator->make();
    }

    public function makeResource(string $entityName)
    {
        $resourceGenerator = new ResourceGenerator($this->templateWriter->newContext());
        $entityName = EntityName::fromString($entityName);

        $resourceGenerator->make(
            $entityName,
            $this->getEntityInformation($entityName),
            $this->getEntityFields($entityName)
        );
    }

    public function makeSupport()
    {
        $supportGenerator = new SupportGenerator($this->templateWriter->newContext());
        $supportGenerator->make();
    }

    public function getEntityInformation(EntityName $entityName): EntityInformationDTO
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

    protected function getEntityFields(EntityName $entityName): EntityFieldCollection
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

    public function __destruct()
    {
        if ($this->cache == false) {
            $this->cacheWriter->clearCache();
        }
    }
}
