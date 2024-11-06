<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Msmm\HyperfRedisLock;

class LockScripts
{
    /**
     * release lock.
     */
    public static function releaseLock(): string
    {
        return <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    return redis.call("del",KEYS[1])
else
    return 0
end
LUA;
    }
}
