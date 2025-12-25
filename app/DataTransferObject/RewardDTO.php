<?php

namespace App\DataTransferObject;

use Illuminate\Http\UploadedFile;

class RewardDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $points,
        public readonly ?UploadedFile $logo = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            description: $data['description'] ?? null,
            points: $data['points'],
            logo: $data['logo'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'points' => $this->points,
            'logo' => $this->logo,
        ];
    }
}
