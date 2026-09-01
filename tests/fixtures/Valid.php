<?php

declare(strict_types=1);

namespace EaglePhpCodeQuality\Tests\Fixtures;

final class Valid
{
    public function greet(string $name): string
    {
        return 'Hello, ' . $name;
    }
}
