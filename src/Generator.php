<?php

namespace Anteris\Autotask\Generator;

use Exception;
use GuzzleHttp\Client as HttpClient;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * This class is the parent generator for Data Transfer Objects and services.
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

    /** @var Environment An instance of Twig Templating Engine. */
    protected $twig;

    /**
     * Sets up the class to create a new endpoint.
     * 
     * @param  string  $username  The API user's username.
     * @param  string  $secret    The API user's token.
     * @param  string  $baseUri   The API URL to be used for requests.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(
        string $username,
        string $secret,
        string $integrationCode,
        string $baseUri = null
    ) {
        // If the Base URL is not set, find it based on the user
        if (!$baseUri) {
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
        }

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
    }

    /**
     * Returns an instance of the endpoint generator, ready to go.
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function endpoint(): EndpointGenerator
    {
        if (!isset($this->classCache['endpoint'])) {
            $this->classCache['endpoint'] = new EndpointGenerator($this->client, $this->twig);
        }

        return $this->classCache['endpoint'];
    }

    /**
     * Returns an instance of the support generator, ready to go.
     * This generates files that are used across multiple domains and for the
     * most part are statically written.
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function support(): SupportGenerator
    {
        if (!isset($this->classCache['support'])) {
            $this->classCache['support'] = new SupportGenerator($this->twig);
        }

        return $this->classCache['support'];
    }
}
