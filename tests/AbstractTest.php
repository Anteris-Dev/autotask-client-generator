<?php

namespace Tests;

use Dotenv\Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

abstract class AbstractTest extends TestCase
{
    const TEST_DIR = __DIR__ . '/tests/';

    /** @var string The API username for interacting with the Autotask API. */
    protected string $username;

    /** @var string The API secret for interacting with the Autotask API. */
    protected string $secret;

    /** @var string The API integration code for interacting with the Autotask API. */
    protected string $integrationCode;

    /**
     * Sets up the classes for interacting with the client.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function setUp(): void
    {
        /**
         * Start by trying to load environment variables. If we are running v5
         * of Dotenv, we need to create an unsafe immutable in order to use
         * environment variables in CI.
         */
        if (file_exists(__DIR__ . '/../.env')) {
            if (method_exists(Dotenv::class, 'createUnsafeImmutable')) {
                $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
            } else {
                $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            }

            $dotenv->load();
        }

        /**
         * Here we are going to run checks for each environment variable and throw an
         * exception if there are problems.
         */
        $username   = getenv('AUTOTASK_API_USERNAME');
        $secret     = getenv('AUTOTASK_API_SECRET');
        $ic         = getenv('AUTOTASK_API_INTEGRATION_CODE');

        if (!$username) {
            throw new Exception('Unable to find find AUTOTASK_API_USERNAME env variable!');
        }

        if (!$secret) {
            throw new Exception('Unable to find find AUTOTASK_API_SECRET env variable!');
        }

        if (!$ic) {
            throw new Exception('Unable to find find AUTOTASK_API_INTEGRATION_CODE env variable!');
        }

        /**
         * Now we can store the values.
         */
        $this->username = $username;
        $this->secret = $secret;
        $this->integrationCode = $ic;

        /**
         * Now make our test directory
         */
        mkdir(self::TEST_DIR);
    }

    /**
     * Cleans up our testing.
     */
    protected function tearDown(): void
    {
        /**
         * Recurses our test directory and deletes everything in there.
         */
        $directory = self::TEST_DIR;
        $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($directory);
    }
}
