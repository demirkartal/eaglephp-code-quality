<?php

declare(strict_types=1);

namespace EaglePhpCodeQuality\Tests\Fixtures;

class NewStaticProbeParent
{
    public function __construct(private int $x = 0) {}

    public function make(): static
    {
        return new static(1);
    }
}

class NewStaticProbeChild extends NewStaticProbeParent
{
    public function __construct(private string $s) {}
}
