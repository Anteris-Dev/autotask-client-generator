<?php

namespace Anteris\Autotask\Generator;

use Exception;
use Twig\Environment;

/**
 * Helps with writing the files we generate.
 */
abstract class AbstractFileWriter
{
    /** @var string Determines the directory any files will be written to. */
    protected string $outputDirectory = __DIR__;

    /** @var bool Determines whether the file writer should overwrite the file if it exists. */
    protected bool $overwrite = false;

    /** @var string Sets a subdirectory that any files will be written to. */
    protected string $subDirectory = '';

    /** @var Environment Twig Engine for writing files that are templates. */
    protected Environment $twig;

    /**
     * Sets up the class.
     * 
     * @param  Environment  $twig  A Twig environment for writing templated files.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Sets the directory that we will write files to.
     * 
     * @param  string  $directory  The directory to write to.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function setOutputDirectory(string $directory): void
    {
        $directory = rtrim($directory, '\\/') . '/';

        if (!is_dir($directory) && !mkdir($directory, 0777, true)) {
            throw new Exception('Directory does not exist and we were unable to create it!');
        }

        $this->outputDirectory = $directory;
    }

    /**
     * Sets a subdirectory that we will write files to.
     * 
     * @param  string  $directory  The directory to write to.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function setSubDirectory(string $directory): void
    {
        $directory = rtrim($directory, '\\/') . '/';

        if (
            !is_dir($this->outputDirectory . $directory) &&
            !mkdir($this->outputDirectory . $directory, 0777, true)
        ) {
            throw new Exception('Directory does not exist and we were unable to create it!');
        }

        $this->subDirectory = $directory;
    }

    /**
     * Changes whether overwrite is enabled.
     * 
     * @param  bool  $overwrite  Whether or not overwrite should be enabled.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function setOverwrite(bool $overwrite = true): void
    {
        $this->overwrite = $overwrite;
    }

    /**
     * Determines whether or not a file should be written based on overwrite rules, etc.
     * 
     * @param  string  $filename  The file we are testing.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function shouldWriteFile(string $filename): bool
    {
        if (
            file_exists($this->outputDirectory . $this->subDirectory . $filename) &&
            $this->overwrite === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Writes the specified contents to the specified file.
     * 
     * @param  string  $filename  The file to be written to.
     * @param  mixed   $contents  The contents to write.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function writeFile(string $filename, $contents)
    {
        if (! $this->shouldWriteFile($filename)) {
            return;
        }

        if (! file_put_contents($this->outputDirectory . $this->subDirectory . $filename, $contents)) {
            throw new Exception("Unable to write to the file $filename!");
        }
    }

    /**
     * Takes a Twig templace, renders it, and writes it to the specified file.
     * 
     * @param  string  $template      Name of the Twig template to be used.
     * @param  string  $filename      Name of the file to be written.
     * @param  array   $replacements  The Twig replacements to be made.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function writeTemplate(string $template, string $filename, array $replacements = [])
    {
        $this->writeFile(
            $filename,
            $this->twig->render($template, $replacements)
        );
    }
}
