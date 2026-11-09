<?php

declare(strict_types=1);

namespace app\tests\unit\scaffold;

use Codeception\Test\Unit;
use yii\helpers\FileHelper;
use yii\scaffold\Manifest\{FileMapping, FileMode, ManifestExpander, ManifestSchema};
use yii\scaffold\Scaffold\Lock\Hasher;
use yii\scaffold\Scaffold\Modes\{ApplyOutcome, ReplaceMode};

use function bin2hex;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function mkdir;
use function random_bytes;
use function sys_get_temp_dir;

use const JSON_THROW_ON_ERROR;

/**
 * Verifies the files and modes distributed by the scaffold provider manifest.
 *
 * Test coverage.
 * - Includes the complete PHP view layout in replace mode.
 * - Preserves the public asset placeholder.
 * - Excludes provider-only test configuration and its web entry point.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class ViewManifestTest extends Unit
{
    private string $temporaryRoot = '';

    public function testManifestIncludesLayoutPartialsAndPreservesConsumerChangesOnReapplication(): void
    {
        $projectRoot = dirname(__DIR__, 3);
        $manifestContent = file_get_contents($projectRoot . '/scaffold.json');

        self::assertIsString($manifestContent);

        /** @var array<mixed> $rawManifest */
        $rawManifest = json_decode($manifestContent, true, flags: JSON_THROW_ON_ERROR);
        $manifest = (new ManifestSchema())->validate($rawManifest);
        $mappings = (new ManifestExpander())->expand(
            $manifest,
            $projectRoot,
            'yii2-extensions/app-base',
        );
        /** @var array<string, FileMapping> $mappingsByDestination */
        $mappingsByDestination = [];

        foreach ($mappings as $mapping) {
            $mappingsByDestination[$mapping->destination] = $mapping;
        }

        foreach (
            [
                'resources/views/layouts/main.php',
                'resources/views/layouts/head.php',
                'resources/views/layouts/component/alerts.php',
                'resources/views/layouts/component/menu.php',
            ] as $layoutFile
        ) {
            $layoutMapping = $mappingsByDestination[$layoutFile] ?? null;

            self::assertInstanceOf(FileMapping::class, $layoutMapping);
            self::assertSame(FileMode::Replace, $layoutMapping->mode);
        }

        foreach (['config/test.php', 'config/test_db.php', 'public/index-test.php'] as $providerTestFile) {
            self::assertArrayNotHasKey($providerTestFile, $mappingsByDestination);
        }

        $headMapping = $mappingsByDestination['resources/views/layouts/head.php'] ?? null;

        self::assertInstanceOf(FileMapping::class, $headMapping);

        $this->temporaryRoot = sys_get_temp_dir() . '/app-base-scaffold-' . bin2hex(random_bytes(8));

        self::assertTrue(mkdir($this->temporaryRoot, 0o777, true));

        $mode = new ReplaceMode();
        $hasher = new Hasher();
        $firstApplication = $mode->apply($headMapping, $this->temporaryRoot, $hasher, null);
        $consumerFile = $this->temporaryRoot . '/resources/views/layouts/head.php';

        self::assertSame(ApplyOutcome::Written, $firstApplication->outcome);
        self::assertIsString(file_get_contents($consumerFile));
        self::assertNotFalse(file_put_contents($consumerFile, "<?php // consumer-owned change\n"));

        $reapplication = $mode->apply(
            $headMapping,
            $this->temporaryRoot,
            $hasher,
            $firstApplication->newHash,
        );

        self::assertSame(ApplyOutcome::Skipped, $reapplication->outcome);
        self::assertSame("<?php // consumer-owned change\n", file_get_contents($consumerFile));
    }

    public function testManifestPreservesExistingPublicAssetPlaceholder(): void
    {
        $projectRoot = dirname(__DIR__, 3);
        $manifestContent = file_get_contents($projectRoot . '/scaffold.json');

        self::assertIsString($manifestContent);

        /** @var array<mixed> $rawManifest */
        $rawManifest = json_decode($manifestContent, true, flags: JSON_THROW_ON_ERROR);
        $manifest = (new ManifestSchema())->validate($rawManifest);
        $mappings = (new ManifestExpander())->expand(
            $manifest,
            $projectRoot,
            'yii2-extensions/app-base',
        );
        $assetPlaceholder = null;

        foreach ($mappings as $mapping) {
            if ($mapping->destination === 'public/assets/.gitignore') {
                $assetPlaceholder = $mapping;

                break;
            }
        }

        self::assertInstanceOf(FileMapping::class, $assetPlaceholder);
        self::assertSame(FileMode::Preserve, $assetPlaceholder->mode);
    }

    protected function tearDown(): void
    {
        if ($this->temporaryRoot !== '') {
            FileHelper::removeDirectory($this->temporaryRoot);
        }

        parent::tearDown();
    }
}
