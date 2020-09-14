<?php

namespace Anteris\Autotask\Generator\Writers;

use Exception;

class CacheWriter extends FileWriter
{
    /** @var array Stores cached items in memory. */
    protected array $cache = [];

    /**
     * Sets up the class to start writing to the cache.
     */
    public function __construct(string $baseDir)
    {
        parent::__construct($baseDir);
        
        // Ensure we always overwrite
        $this->overwrite = true;

        // Create the cache directory
        $this->createAndEnterDirectory('.cache');
    }

    /**
     * Caches a file (in memory or preferably, in a file).
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function cache(string $pointer, $content)
    {
        $pointer               = md5($pointer);
        $this->cache[$pointer] = $content;

        // If the file caching fails, don't make a big stink.
        try {
            $this->createFile($pointer, serialize($content));
        } catch (Exception $exception) {
            echo 'Unable to cache to filesystem!';
        }
    }

    /**
     * Deletes the cache from the system.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function clearCache()
    {
        $this->enterDirectory('../');
        $this->deleteDirectory('.cache');
    }

    /**
     * Retrieves a cached file from memory or file. If it does not exist, return false.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function getCached(string $pointer)
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

    /**
     * Lightweight function to see if something is in the cache.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function inCache(string $pointer): bool
    {
        $pointer = md5($pointer);

        if (isset($this->cache[$pointer]) == true) {
            return true;
        }

        if ($this->fileExists($pointer)) {
            return true;
        }

        return false;
    }

    /**
     * Instead of deleting the cache entirely, recreates it.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function resetCache()
    {
        $this->clearCache();
        $this->createAndEnterDirectory('.cache');
    }
}
