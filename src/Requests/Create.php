<?php

declare(strict_types=1);

namespace Codin\Healthchecks\Requests;

class Create
{
    protected array $params;

    public function __construct(
        string $name,
        array $tags = [],
        ?string $desc = null,
        int $timeout = 86400,
        int $grace = 3600,
        ?string $schedule = null,
        string $tz = 'UTC',
        ?array $channels = null
    ) {
        $this->params = [
            'name' => $name,
            'tags' => implode(' ', $tags),
            'desc' => $desc,
            'timeout' => $timeout,
            'grace' => $grace,
            'schedule' => $schedule,
            'tz' => $tz,
            'channels' => is_array($channels) ? implode(',', $channels) : null,
        ];
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
