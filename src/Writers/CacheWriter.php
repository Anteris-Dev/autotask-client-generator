<?php

namespace Anteris\Autotask\Generator\Writers;

use Exception;

class CacheWriter extends FileWriter
{
    protected array $cache = [];

    public function __construct(string $baseDir)
    {
        parent::__construct($baseDir);

        $this->overwrite = true;
        $this->createAndEnterDirectory('.cache');
    }

    public function cache(string $pointer, $content): void
    {
        $pointer = md5($pointer);
        $this->cache[$pointer] = $content;

        // If the file caching fails, don't make a big stink.
        try {
            $this->createFile($pointer, serialize($content));
        } catch (Exception $exception) {
            echo 'Unable to cache to filesystem!';
        }
    }

    public function clearCache(): void
    {
        $this->enterDirectory('../');
        $this->deleteDirectory('.cache');
    }

    public function getCached(string $pointer): string|false
    {
        $pointer = md5($pointer);

        if (isset($this->cache[$pointer]) == true) {
            return $this->cache[$pointer];
        }

        if ($this->fileExists($pointer)) {
            return unserialize($this->getFile($pointer));
        }

        return false;
    }

    public function inCache(string $pointer): bool
    {
        $pointer = md5($pointer);

        if (isset($this->cache[$pointer])) {
            return true;
        }

        if ($this->fileExists($pointer)) {
            return true;
        }

        return false;
    }

    public function resetCache(): void
    {
        $this->clearCache();
        $this->createAndEnterDirectory('.cache');
    }
}
