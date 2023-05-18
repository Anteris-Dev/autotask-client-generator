<?php

namespace Anteris\Autotask\Generator\Writers;

use DirectoryIterator;
use Exception;

class FileWriter
{
    protected string $baseDir;
    protected string $originalBaseDir;
    protected bool $overwrite = false;

    public function __construct(string $baseDir) {
        $this->originalBaseDir = $this->baseDir = rtrim($baseDir, '\\/') . '/';
    }

    /**
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function createAndEnterDirectory(string $directory): void
    {
        $this->createDirectory($directory);
        $this->enterDirectory($directory);
    }

    /**
     * @throws Exception
     */
    public function deleteDirectory(string $directory): void
    {
        $deleted = $this->deleteFilesInDirectory(
            $this->joinPaths($this->baseDir, $directory)
        );

        if (! $deleted) {
            throw new Exception("Unable to delete $directory!");
        }
    }

    /**
     * @throws Exception
     */
    public function deleteFile(string $filename): void
    {
        $deleted = unlink(
            $this->baseDir . $filename
        );

        if (! $deleted) {
            throw new Exception("Unable to delete $filename!");
        }
    }

    public function directoryExists(string $directory): bool
    {
        return is_dir(
            $this->joinPaths($this->baseDir, $directory)
        );
    }

    /**
     * @throws Exception
     */
    public function enterDirectory(string $directory): void
    {
        if (! $this->directoryExists($directory)) {
            throw new Exception("Directory does not exist!");
        }

        $this->baseDir = $this->joinPaths($this->baseDir, $directory);
    }

    public function fileExists(string $filename): bool
    {
        return file_exists($this->baseDir . $filename);
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getFile(string $filename): false|string
    {
        return file_get_contents($this->baseDir . $filename);
    }

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

    public function newContext(): FileWriter
    {
        $context = new static($this->originalBaseDir);
        $context->setOverwrite($this->overwrite);

        return $context;
    }

    public function resetContext(): void
    {
        $this->baseDir = $this->originalBaseDir;
    }

    public function setOverwrite(bool $overwrite = true): void
    {
        $this->overwrite = $overwrite;
    }

    protected function deleteFilesInDirectory(string $directory): bool
    {
        foreach (new DirectoryIterator($directory) as $item) {
            if ($item->isDot()) {
                continue;
            }

            if ($item->isFile()) {
                unlink($item->getPathname());
            }

            if ($item->isDir()) {
                $this->deleteFilesInDirectory($item->getPathName());
            }
        }

        return rmdir($directory);
    }
}
