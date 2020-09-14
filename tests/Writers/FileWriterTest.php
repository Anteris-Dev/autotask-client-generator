<?php

namespace Tests\Writers;

use Anteris\Autotask\Generator\Writers\FileWriter;
use Illuminate\Support\Facades\File;
use Tests\AbstractTest;

class FileWriterTest extends AbstractTest
{
    /**
     * @covers \Anteris\Autotask\Generator\Writers\FileWriter
     */
    public function test_it_can_create_class()
    {
        /**
         * Assert the class is an instance of our class
         */
        $this->assertInstanceOf(FileWriter::class, $this->createNewFileWriter());
    }

    /**
     * @covers \Anteris\Autotask\Generator\Writers\FileWriter
     */
    public function test_it_can_create_directory()
    {
        $directory = 'testing';
        $writer    = $this->createNewFileWriter();

        $this->assertDirectoryDoesNotExist(self::TEST_DIR . $directory);
        $writer->createAndEnterDirectory($directory);
        $this->assertDirectoryExists(self::TEST_DIR . $directory);
        $this->assertEquals(
            self::TEST_DIR . $directory . '/',
            $writer->getBaseDir()
        );
    }

    /**
     * @covers \Anteris\Autotask\Generator\Writers\FileWriter
     */
    public function test_it_can_delete_directory()
    {
        $directory = self::TEST_DIR . 'testing';
        mkdir($directory);
        
        $this->assertDirectoryExists($directory);
        $this->createNewFileWriter()->deleteDirectory('testing');
        $this->assertDirectoryDoesNotExist($directory);
    }

    /**
     * @covers \Anteris\Autotask\Generator\Writers\FileWriter
     */
    public function test_it_can_create_file()
    {
        $testFile = self::TEST_DIR . 'test.txt';

        $this->assertFileDoesNotExist($testFile);
        $this->createNewFileWriter()->createFile('test.txt', 'hello world');
        $this->assertFileExists($testFile);
        $this->assertEquals('hello world', file_get_contents($testFile));
    }

    /**
     * @covers \Anteris\Autotask\Generator\Writers\FileWriter
     */
    public function test_it_can_read_file()
    {
        $testFile = self::TEST_DIR . 'test.txt';
        file_put_contents($testFile, 'hello world');

        $this->assertEquals(
            'hello world',
            $this->createNewFileWriter()->getFile('test.txt')
        );
    }

    /**
     * @covers \Anteris\Autotask\Generator\Writers\FileWriter
     */
    public function test_it_can_delete_file()
    {
        $testFile = self::TEST_DIR . 'test.txt';
        file_put_contents($testFile, 'testing.');

        $this->assertFileExists($testFile);
        $this->createNewFileWriter()->deleteFile('test.txt');
        $this->assertFileDoesNotExist($testFile);
    }

    /**
     * Creates a new instance of the file writer for use.
     */
    protected function createNewFileWriter()
    {
        return new FileWriter(self::TEST_DIR);
    }
}
