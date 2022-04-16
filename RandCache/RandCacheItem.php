<?php

namespace RandCache;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Psr\Cache\CacheItemInterface;

class RandCacheItem implements CacheItemInterface
{
    private string $key;
    private int $value;
    private bool $isHit;
    private ?DateTimeInterface $expiresAt;

    public function __construct(string                $key,
                                bool                  $isHit,
                                int                   $value = null,
                                DateTimeInterface|int $expiresAt = null)
    {
        $this->key = $key;
        $this->isHit = $isHit;
        $this->value = $value;

        if ($expiresAt instanceof DateTimeInterface) {
            $this->expiresAt = $expiresAt;

        } else {
            $this->expiresAt = (new DateTime())->setTimestamp($expiresAt);
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get()
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set($value)
    {
        $this->value = $value;
    }

    public function expiresAt($expiration): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * @throws Exception
     */
    public function expiresAfter($time)
    {
        if ($time === null) {
            $this->expiresAt = null;
            return;
        }

        $expires = new DateTimeImmutable();
        if (is_int($time)) {
            $time = new DateInterval('PT' . $time . 'S');
        }

        $this->expiresAt = $expires->add($time);
    }

    public function toString(): string
    {
        return json_encode([
            'id' => $this->getKey(),
            'value' => $this->get(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function getExpiresAtToTimestamp(): int
    {
        if ($this->expiresAt instanceof DateTimeInterface) {
            return $this->expiresAt->getTimestamp();
        }
        return (int)$this->expiresAt;
    }
}