<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Msmm\HyperfRedisLock;

use Hyperf\Stringable\Str;
use Hyperf\Support\Traits\InteractsWithTime;

abstract class Lock implements LockContract
{
    use InteractsWithTime;

    /**
     * The name of the lock.
     */
    protected string $name;

    protected int $seconds;

    /**
     * The scope identifier of this lock.
     * @var string
     */
    protected mixed $owner;

    public function __construct($name, $seconds, $owner = null)
    {
        if (is_null($owner)) {
            $owner = Str::random();
        }
        $this->name = $name;
        $this->seconds = $seconds;
        $this->owner = $owner;
    }

    /**
     * Attempt to acquire the lock.
     */
    abstract public function acquire(): bool;

    /**
     * Release the lock.
     */
    abstract public function release(): mixed;

    /**
     * Attempt to acquire the lock.
     * @return bool|mixed
     */
    public function get(?callable $callback = null, ?callable $finally = null): mixed
    {
        $result = $this->acquire();
        if ($result && is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }
        if (!$result && is_callable($finally)) {
            return $finally();
        }

        return $result;
    }

    /**
     * Attempt to acquire the lock for the given number of seconds.
     * @return bool|mixed
     * @throws LockTimeoutException
     */
    public function block(int $seconds, ?callable $callback = null): mixed
    {
        $starting = $this->currentTime();
        while (!$this->acquire()) {
            usleep(250 * 1000);
            if ($this->currentTime() - $seconds >= $starting) {
                throw new LockTimeoutException();
            }
        }

        if (is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }

        return true;
    }

    /**
     * Returns the current owner of the lock.
     */
    public function owner(): ?string
    {
        return $this->owner;
    }

    /**
     * Returns the owner value written into the driver for this lock.
     */
    abstract protected function getCurrentOwner(): string;

    /**
     * Determines whether this lock is allowed to release the lock in the driver.
     */
    protected function isOwnedByCurrentProcess(): bool
    {
        return $this->getCurrentOwner() === $this->owner;
    }
}
