<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Msmm\HyperfRedisLock;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
