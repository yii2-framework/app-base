<?php

declare(strict_types=1);

namespace app\tests\support\spies;

use app\commands\HelloController;
use Override;

use function strlen;

/**
 * Stub class for {@see HelloController} that captures output sent to STDOUT in a buffer for inspection in tests.
 */
final class HelloControllerSpy extends HelloController
{
    /**
     * Concatenated capture of every string passed to {@see stdout()}.
     */
    public string $stdoutBuffer = '';

    /**
     * @param string $string String to buffer instead of writing to `STDOUT`.
     */
    #[Override]
    public function stdout($string)
    {
        $this->stdoutBuffer .= (string) $string;

        return strlen((string) $string);
    }
}
