<?php

namespace RandCache;

use Exception;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class RandFileCachePool implements CacheItemPoolInterface
{
    private string $directory;
    private int $defaultTTL = 0;
    private int $maxNumber = 1000000;

    /**
     * @throws Exception
     */
    public function __construct(string $namespace = '', ?string $directory = null, int $defaultTTL = 180)
    {
        $this->directory = rtrim($directory ?? sys_get_temp_dir(), '/')
            . DIRECTORY_SEPARATOR . 'randcache' . ($namespace ? '_' . $namespace : '');

        $this->defaultTTL = $defaultTTL;

        if (file_exists($this->directory)) {
            return;
        }

        if (!mkdir($this->directory, 0777, true)) {
            throw new Exception('Ошибка доступа к директории ' . $this->directory);
        }
    }

    public function setMaxGenerateNumber(int $num): static
    {
        $this->maxNumber = $num;
        return $this;
    }

    public function getMaxGenerateNumber(): int
    {
        return $this->maxNumber;
    }

    public function getItem($key): RandCacheItem
    {
        return $this->getFileData($key);
    }

    public function getItems(array $keys = []): array
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getFileData($key);
        }
        return $items;
    }

    public function hasItem($key): bool
    {
        $file = $this->filePath($key);
        return file_exists($file) && $this->checkFileExpiry($file, time());
    }

    public function clear(): bool
    {
        return $this->removeDir($this->directory);
    }

    public function deleteItem($key): bool
    {
        $path = $this->filePath($key);
        return !file_exists($path) || unlink($path);
    }

    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        $dir = $this->fileDir($item->getKey());
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return file_put_contents($this->filePath($item->getKey()),
            $item->getExpiresAtToTimestamp() . "\n" . $item->get());
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->save($item);
    }

    public function commit(): bool
    {
        return true;
    }

    private function fileDir($key): string
    {
        $dir = substr($key, 0, 3);
        return $this->directory . DIRECTORY_SEPARATOR . $dir;
    }

    private function filePath($key): string
    {
        return $this->fileDir($key) . DIRECTORY_SEPARATOR . $key;
    }

    private function getFileData(string $key): RandCacheItem
    {
        $file = $this->filePath($key);
        if (!file_exists($file)) {
            return $this->generate($key);
        }

        $rows = file($file);
        if (empty($rows) || count($rows) < 2) {
            return $this->generate($key);
        }

        $expiresAt = (int)trim($rows[0]);
        if ($expiresAt < time()) {
            return $this->generate($key);
        }

        $value = (int)trim($rows[1]);
        return new RandCacheItem($key, true, $value, $expiresAt);
    }

    private function generate(string $key): RandCacheItem
    {
        return new RandCacheItem($key, true, rand(0, $this->maxNumber),
            time() + $this->defaultTTL);
    }

    private function checkFileExpiry(string $file, int $time): bool
    {
        $timestamp = $this->getExpiryTimestamp($file);
        if ($timestamp === null) {
            return true;
        }

        $expired = $timestamp < $time;
        if ($expired) {
            unlink($file);
        }
        return !$expired;
    }

    private function getExpiryTimestamp(string $file): ?int
    {
        $fp = fopen($file, 'r');
        if ($buffer = fgets($fp, 16) === false) {
            return null;
        }
        fclose($fp);

        $time = (int)trim($buffer);
        return $time == 9999999999 ? null : $time;
    }

    private function removeDir(string $path): bool
    {
        if (is_file($path)) {
            return unlink($path);
        }

        if (is_dir($path)) {
            foreach (scandir($path) as $p) {
                if ($p == '.' || $p == '..') {
                    continue;
                }

                $this->removeDir($path . DIRECTORY_SEPARATOR . $p);
            }

            return rmdir($path);
        }

        return true;
    }
}