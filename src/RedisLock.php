<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Msmm\HyperfRedisLock;

use Hyperf\Redis\Redis;

/**
 * Class RedisLock.
 */
class RedisLock extends Lock
{
    protected Redis $redis;

    public function __construct($redis, $name, $seconds, $owner = null)
    {
        parent::__construct($name, $seconds, $owner);
        $this->redis = $redis;
    }

    /**
     * Acquire the lock.
     * @throws \RedisException
     */
    public function acquire(): bool
    {
        $result = $this->redis->setnx($this->name, $this->owner);

        if (intval($result) === 1 && $this->seconds > 0) {
            $this->redis->expire($this->name, $this->seconds);
        }

        return intval($result) === 1;
    }

    /**
     * Release the lock.
     * @throws \RedisException
     */
    public function release(): bool
    {
        if ($this->isOwnedByCurrentProcess()) {
            $res = $this->redis->eval(LockScripts::releaseLock(), ['name' => $this->name, 'owner' => $this->owner], 1);
            return $res == 1;
        }
        return false;
    }

    /**
     * Force release the lock.
     * @throws \RedisException
     */
    public function forceRelease(): bool
    {
        $r = $this->redis->del($this->name);
        return $r == 1;
    }

    /**
     * Returns the current owner of the lock.
     * @throws \RedisException
     */
    protected function getCurrentOwner(): string
    {
        return $this->redis->get($this->name);
    }
}
