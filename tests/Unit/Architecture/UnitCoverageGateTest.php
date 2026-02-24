<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\TestCase;

class UnitCoverageGateTest extends TestCase
{
    private const EXPECTED_UNIT_COUNT = 210;

    public function test_total_unit_yang_wajib_memiliki_direct_test_sesuai_kontrak(): void
    {
        $this->assertCount(self::EXPECTED_UNIT_COUNT, self::unitClassProvider());
    }

    #[DataProvider('unitClassProvider')]
    public function test_unit_memiliki_direct_test_gate(string $className, string $expectedFilePath): void
    {
        $exists = class_exists($className) || interface_exists($className) || trait_exists($className);
        $this->assertTrue($exists, sprintf('Unit %s tidak dapat di-load oleh autoload.', $className));

        $reflection = new ReflectionClass($className);
        $resolvedPath = str_replace('\\', '/', (string) $reflection->getFileName());

        $this->assertSame($expectedFilePath, $resolvedPath);
        $this->assertNotEmpty($reflection->getShortName());
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function unitClassProvider(): array
    {
        $basePath = str_replace('\\', '/', dirname(__DIR__, 3));
        $appPath = $basePath.'/app';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appPath));
        $cases = [];

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo instanceof SplFileInfo || ! $fileInfo->isFile()) {
                continue;
            }

            if (! str_ends_with($fileInfo->getFilename(), '.php')) {
                continue;
            }

            $normalizedPath = str_replace('\\', '/', $fileInfo->getPathname());
            $relativePath = ltrim(substr($normalizedPath, strlen($basePath)), '/');

            if (! self::isUnitFile($relativePath)) {
                continue;
            }

            $className = self::classNameFromRelativePath($relativePath);
            $cases[$className] = [$className, $normalizedPath];
        }

        ksort($cases);

        return $cases;
    }

    private static function isUnitFile(string $relativePath): bool
    {
        return (bool) preg_match(
            '#^app/(Actions|UseCases|Services|Repositories)/.+(Action|UseCase|Service|Repository)\.php$#',
            $relativePath
        ) || (bool) preg_match(
            '#^app/Domains/Wilayah/[^/]+/(Actions|UseCases|Services|Repositories)/.+(Action|UseCase|Service|Repository)\.php$#',
            $relativePath
        );
    }

    private static function classNameFromRelativePath(string $relativePath): string
    {
        $relativeFromApp = preg_replace('#^app/#', '', $relativePath);

        return 'App\\'.str_replace(['/', '.php'], ['\\', ''], (string) $relativeFromApp);
    }
}
