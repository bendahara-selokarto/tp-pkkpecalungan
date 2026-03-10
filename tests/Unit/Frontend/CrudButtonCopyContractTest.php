<?php

namespace Tests\Unit\Frontend;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CrudButtonCopyContractTest extends TestCase
{
    public function test_tombol_tambah_crud_memakai_label_generik_tanpa_objek_eksplisit(): void
    {
        $pageFiles = array_map(
            static fn ($file): string => $file->getPathname(),
            File::allFiles(base_path('resources/js/Pages'))
        );

        $paths = array_merge(
            $pageFiles,
            [base_path('resources/js/admin-one/components/DataWargaAnggotaTable.vue')]
        );

        $violations = [];

        foreach ($paths as $path) {
            $content = file_get_contents($path);

            $this->assertNotFalse($content, "File {$path} tidak dapat dibaca.");

            $lines = preg_split('/\R/', $content);
            if (! is_array($lines)) {
                continue;
            }

            foreach ($lines as $lineNumber => $line) {
                if (preg_match('/\+\s+Tambah\s+\S/u', $line) !== 1) {
                    continue;
                }

                $violations[] = sprintf(
                    '%s:%d => %s',
                    str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
                    $lineNumber + 1,
                    trim($line)
                );
            }
        }

        $this->assertSame(
            [],
            $violations,
            "Label tombol tambah CRUD harus memakai pola generik `+ Tambah`.\n".implode("\n", $violations)
        );
    }
}
