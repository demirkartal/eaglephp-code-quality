<?php

declare(strict_types=1);

namespace EaglePhpCodeQuality\Tests\Fixtures;

/**
 * Golden PHPDoc/format probe — kept compliant with shared pint.json.
 */
final class PintPhpdocProbe
{
    /** @var list<string> */
    private array $items = [];

    /**
     * Probe method.
     *
     * @param string $name  Display name
     * @param int    $count Item count
     *
     * @return list<string> Ordered labels
     */
    public function build(string $name, int $count): array
    {
        return [$name, (string) $count];
    }
}
