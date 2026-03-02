<?php

namespace Database\Factories;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Wilayah\Arsip\Models\ArsipDocument>
 */
class ArsipDocumentFactory extends Factory
{
    protected $model = ArsipDocument::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);
        $extension = 'pdf';
        $storedFilename = fake()->uuid().'.'.$extension;
        $level = fake()->randomElement(['desa', 'kecamatan']);

        return [
            'title' => $title,
            'description' => fake()->optional()->sentence(),
            'original_name' => $title.'.'.$extension,
            'file_path' => 'arsip-documents/'.$storedFilename,
            'mime_type' => 'application/pdf',
            'extension' => $extension,
            'size_bytes' => fake()->numberBetween(1024, 1024 * 500),
            'is_global' => false,
            'level' => $level,
            'area_id' => fn () => $this->resolveAreaId($level),
            'download_count' => 0,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    private function resolveAreaId(string $level): int
    {
        if ($level === 'kecamatan') {
            return (int) Area::query()->create([
                'name' => 'Kecamatan '.fake()->unique()->city(),
                'level' => 'kecamatan',
            ])->id;
        }

        $kecamatan = Area::query()->create([
            'name' => 'Kecamatan '.fake()->unique()->city(),
            'level' => 'kecamatan',
        ]);

        return (int) Area::query()->create([
            'name' => 'Desa '.fake()->unique()->streetName(),
            'level' => 'desa',
            'parent_id' => $kecamatan->id,
        ])->id;
    }
}
