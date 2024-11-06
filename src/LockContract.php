<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Msmm\HyperfRedisLock;

interface LockContract
{
    /**
     * Attempt to acquire the lock.
     */
    public function get(?callable $callback = null, ?callable $finally = null): mixed;

    /**
     * Attempt to acquire the lock for the given number of seconds.
     */
    public function block(int $seconds, ?callable $callback = null): mixed;

    /**
     * Release the lock.
     */
    public function release(): mixed;

    /**
     * Returns the current owner of the lock.
     */
    public function owner(): mixed;

    /**
     * Releases this lock in disregard of ownership.
     */
    public function forceRelease(): mixed;
}
