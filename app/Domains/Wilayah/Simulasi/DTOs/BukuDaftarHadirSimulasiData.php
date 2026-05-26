<?php

namespace App\Domains\Wilayah\Simulasi\DTOs;

use Illuminate\Http\UploadedFile;

class BukuDaftarHadirSimulasiData
{
    public function __construct(
        public readonly string $attendance_date,
        public readonly string $title,
        public readonly string $attendee_name,
        public readonly ?string $institution,
        public readonly ?string $description,
        public readonly string $level,
        public readonly int $area_id,
        public readonly int $created_by,
        public readonly int $tahun_anggaran,
        public readonly ?UploadedFile $file = null,
        public readonly ?string $file_path = null,
        public readonly ?string $original_name = null,
        public readonly ?string $mime_type = null,
        public readonly ?string $extension = null,
        public readonly ?int $size_bytes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            attendance_date: $data['attendance_date'],
            title: $data['title'],
            attendee_name: $data['attendee_name'],
            institution: $data['institution'] ?? null,
            description: $data['description'] ?? null,
            level: $data['level'],
            area_id: (int) $data['area_id'],
            created_by: (int) $data['created_by'],
            tahun_anggaran: (int) $data['tahun_anggaran'],
            file: $data['file'] ?? null,
            file_path: $data['file_path'] ?? null,
            original_name: $data['original_name'] ?? null,
            mime_type: $data['mime_type'] ?? null,
            extension: $data['extension'] ?? null,
            size_bytes: isset($data['size_bytes']) ? (int) $data['size_bytes'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'attendance_date' => $this->attendance_date,
            'title' => $this->title,
            'attendee_name' => $this->attendee_name,
            'institution' => $this->institution,
            'description' => $this->description,
            'level' => $this->level,
            'area_id' => $this->area_id,
            'created_by' => $this->created_by,
            'tahun_anggaran' => $this->tahun_anggaran,
            'file_path' => $this->file_path,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,
            'size_bytes' => $this->size_bytes,
        ];
    }
}
