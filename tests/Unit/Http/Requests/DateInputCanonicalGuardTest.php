<?php

namespace Tests\Unit\Http\Requests;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\TestCase;

class DateInputCanonicalGuardTest extends TestCase
{
    public function test_trait_parses_ui_date_tidak_dipakai_pada_request_aktif(): void
    {
        $requestFiles = $this->collectRequestFiles();
        $filesUsingTrait = [];

        foreach ($requestFiles as $filePath) {
            $content = file_get_contents($filePath);

            if (! is_string($content)) {
                continue;
            }

            if (str_contains($content, 'use ParsesUiDate;')) {
                $filesUsingTrait[] = str_replace('\\', '/', $filePath);
            }
        }

        $this->assertSame([], $filesUsingTrait, 'Trait ParsesUiDate tidak boleh aktif di request canonical.');
    }

    /**
     * @return list<string>
     */
    private function collectRequestFiles(): array
    {
        $basePath = str_replace('\\', '/', base_path());
        $appPath = $basePath.'/app';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appPath));
        $requestFiles = [];

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo instanceof SplFileInfo || ! $fileInfo->isFile()) {
                continue;
            }

            if (! str_ends_with($fileInfo->getFilename(), 'Request.php')) {
                continue;
            }

            $normalizedPath = str_replace('\\', '/', $fileInfo->getPathname());
            if (str_contains($normalizedPath, '/Requests/')) {
                $requestFiles[] = $normalizedPath;
            }
        }

        sort($requestFiles);

        return $requestFiles;
    }
}
