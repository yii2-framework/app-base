<?php

declare(strict_types=1);

namespace app\tests\unit\scaffold;

use Codeception\Test\Unit;

/**
 * Verifies the access guards on the test web entry point.
 *
 * Test coverage.
 * - Rejects execution outside the explicit test environment.
 * - Rejects non-loopback clients in the test environment.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class PublicEntrypointTest extends Unit
{
    public function testRejectsExecutionOutsideTestEnvironment(): void
    {
        [$output, $errors, $exitCode] = $this->runTestEntrypoint('prod');

        self::assertSame('Not Found', $output);
        self::assertSame('', $errors);
        self::assertSame(0, $exitCode);
    }

    public function testRejectsNonLoopbackClientInTestEnvironment(): void
    {
        [$output, $errors, $exitCode] = $this->runTestEntrypoint('test');

        self::assertSame('Forbidden', $output);
        self::assertSame('', $errors);
        self::assertSame(0, $exitCode);
    }

    /**
     * @return array{string, string, int}
     */
    private function runTestEntrypoint(string $environment): array
    {
        $process = proc_open(
            [PHP_BINARY, dirname(__DIR__, 3) . '/public/index-test.php'],
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            null,
            ['YII_ENV' => $environment],
        );

        if (!is_resource($process)) {
            self::fail('The test entry-point process could not be started.');
        }

        if (!isset($pipes[1], $pipes[2])) {
            proc_close($process);

            self::fail('The test entry-point output pipes were not created.');
        }

        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        self::assertIsString($output);
        self::assertIsString($errors);

        return [$output, $errors, proc_close($process)];
    }
}
