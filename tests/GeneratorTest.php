<?php

namespace Tests;

use Anteris\Autotask\Generator\Generator;

class GeneratorTest extends AbstractTest
{
    const TEST_DIR            = __DIR__ . '/tests/';
    const CLIENT              = self::TEST_DIR . 'src/Client.php';
    const HTTP_CLIENT_FACTORY = self::TEST_DIR . 'src/HttpClientFactory.php';

    /**
     * @covers \Anteris\Autotask\Generator\Generator
     */
    public function test_it_can_create_generator()
    {
        /**
         * Create the instance.
         */
        $generator = new Generator(
            $this->username,
            $this->secret,
            $this->integrationCode,
            self::TEST_DIR,
            true,
            true
        );

        /**
         * Make some assertions.
         */
        $this->assertInstanceOf(Generator::class, $generator);
    }

    /**
     * Ensures the generator can create client files.
     * @covers \Anteris\Autotask\Generator\Generator::makeClient
     */
    public function test_it_can_make_client_files()
    {
        /**
         * Make sure the files do not exist
         */
        $this->assertFileDoesNotExist(self::CLIENT);
        $this->assertFileDoesNotExist(self::HTTP_CLIENT_FACTORY);

        /**
         * Create the instance.
         */
        $generator = new Generator(
            $this->username,
            $this->secret,
            $this->integrationCode,
            __DIR__ . '/tests',
            true,
            true
        );

        /**
         * Generate client files
         */
        $this->assertEmpty($generator->makeClient());

        /**
         * Make sure the files exist
         */
        $this->assertFileExists(self::CLIENT);
        $this->assertFileExists(self::HTTP_CLIENT_FACTORY);
    }
}
