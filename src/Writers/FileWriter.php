<?php

namespace Anteris\Autotask\Generator\Writers;

use Exception;

/**
 * This class exposes an API for writing files.
 */
class FileWriter
{
    /** @var string The root level directory we should be operating under the context of. */
    protected string $baseDir;

    /** @var string Keeps record of the original base directory. */
    protected string $originalBaseDir;

    /** @var bool Determines whether or not the files present should be overwritten. */
    protected bool $overwrite = false;

    /**
     * Sets up the class to start working with this base directory.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(string $baseDir) {
        $this->originalBaseDir = $this->baseDir = rtrim($baseDir, '\\/');
    }

    /**
     * Creates a new directory.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function createDirectory(string $directory): void
    {
        // Early return if the directory exists
        if ($this->directoryExists($directory)) {
            return;
        }

        $created = mkdir(
            $this->joinPaths($this->baseDir, $directory),
            0777,
            true
        );

        if (!$created) {
            throw new Exception("We were unable to create that directory!");
        }
    }

    /**
     * Creates a new file in this current context.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function createFile(string $filename, string $fileContent): void
    {
        // Early return if we are not in overwrite mode and the file exists.
        if ($this->fileExists($filename) && $this->overwrite === false) {
            return;
        }

        $filepath = $this->baseDir . $filename;
        $created  = file_put_contents($filepath, $fileContent);

        if (!$created) {
            throw new Exception("We were unable to create the file at $filepath!");
        }
    }

    /**
     * Creates the requested folder and the sets the current context to that.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function createAndEnterDirectory(string $directory): void
    {
        $this->createDirectory($directory);
        $this->enterDirectory($directory);
    }

    /**
     * Determines whether or not the passed directory exists in the current context.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function directoryExists(string $directory): bool
    {
        return is_dir(
            $this->joinPaths($this->baseDir, $directory)
        );
    }

    /**
     * Sets the current context to that of a sub-directory.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function enterDirectory(string $directory): void
    {
        if (! $this->directoryExists($directory)) {
            throw new Exception("Directory does not exist!");
        }

        $this->baseDir = $this->joinPaths($this->baseDir, $directory);
    }

    /**
     * Determines whether or not the passed filename exists in the current context.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function fileExists(string $filename): bool
    {
        return file_exists($this->baseDir . $filename);
    }

    /**
     * Returns the base directory.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    /**
     * Returns the contents of the requested file.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function getFile(string $filename)
    {
        return file_get_contents($this->baseDir . $filename);
    }

    /**
     * Takes multiple paths and joins them. Trims slashes so that unecessary ones 
     * are not present in the string and forces it to end in a forward slash.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function joinPaths(...$paths): string
    {
        $joinedPath = '';
        $firstPath = true;

        foreach ($paths as $path) {
            if ($firstPath) {
                $joinedPath = rtrim($path, '\\/') . '/';
                $firstPath = false;
                continue;
            }

            $joinedPath .= trim($path, '\\/') . '/';
        }

        return $joinedPath;
    }

    /**
     * Resets the current context to what it was originally.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function resetContext()
    {
        $this->baseDir = $this->originalBaseDir;
    }

    /**
     * Sets whether or not files should be overwritten.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function setOverwrite(bool $overwrite = true) {
        $this->overwrite = $overwrite;
    }
}
