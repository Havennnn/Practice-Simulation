<?php

namespace App\Services\Reward;

use App\Models\Reward;
use App\Models\UploadedFile;
use App\DataTransferObject\RewardDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class RewardService
{
    public function index(int $page, ?array $sort = null): LengthAwarePaginator
    {
        // Start with a base query; don't apply `latest()` yet because that would
        // insert an `order by created_at desc` which overrides requested sorts.
        $query = Reward::query()->with('logoFile');

        // Apply sorting if provided (expected format: ['name' => 'ASC', 'created_at' => 'DESC'])
        if ($sort && is_array($sort) && count($sort) > 0) {
            $allowedFields = ['name', 'code', 'points', 'created_at'];
            foreach ($sort as $field => $direction) {
                if (in_array($field, $allowedFields)) {
                    $query->orderBy($field, strtoupper($direction) === 'DESC' ? 'desc' : 'asc');
                }
            }
        } else {
            // No explicit sorts provided — fallback to latest
            $query->latest();
        }

        return $query->paginate($page)
            ->withQueryString();
    }

    public function store(RewardDTO $dto): Reward
    {
        $data = $dto->toArray();

        if ($dto->logo) {
            $path = $dto->logo->store('rewards', 'public');

            $uploadedFile = UploadedFile::create([
                'name' => $dto->logo->getClientOriginalName(),
                'size' => $dto->logo->getSize(),
                'mime' => $dto->logo->getMimeType(),
                'extension' => $dto->logo->getClientOriginalExtension(),
                'file_path' => $path,
            ]);

            $data['logo_file_id'] = $uploadedFile->id;
        } else {
            $data['logo_file_id'] = null;
        }

        return Reward::create($data);
    }

    public function generateCode(): string
    {
        $attempts = 0;
        do {
            $code = strtoupper(bin2hex(random_bytes(3)));
            $exists = Reward::where('code', $code)->exists();
            $attempts++;
        } while ($exists && $attempts < 10);

        if ($exists) {
            $code .= '-' . substr(uniqid(), -6);
        }

        return $code;
    }
}
