<?php

namespace App\Domains\Wilayah\AccessControl\DTOs;

class AccessControlMatrixFilterData
{
    public function __construct(
        public ?string $scope,
        public ?string $role,
        public ?string $mode
    ) {
    }

    /**
     * @param array{scope?: mixed, role?: mixed, mode?: mixed} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            self::normalize($data['scope'] ?? null),
            self::normalize($data['role'] ?? null),
            self::normalize($data['mode'] ?? null),
        );
    }

    private static function normalize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
