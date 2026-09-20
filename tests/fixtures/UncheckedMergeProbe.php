<?php

declare(strict_types=1);

namespace EaglePhpCodeQuality\Tests\Fixtures;

use Exception;
use LogicException;
use RuntimeException;

final class ConsumerUncheckedException extends Exception {}

final class DomainCheckedException extends Exception {}

/**
 * Probe for uncheckedExceptionClasses merge: no @throws on any method.
 */
final class UncheckedMergeProbe
{
    public function throwsRuntime(): void
    {
        throw new RuntimeException('probe');
    }

    public function throwsLogic(): void
    {
        throw new LogicException('probe');
    }

    public function throwsConsumerUnchecked(): void
    {
        throw new ConsumerUncheckedException('probe');
    }

    public function throwsChecked(): void
    {
        throw new DomainCheckedException('probe');
    }
}
