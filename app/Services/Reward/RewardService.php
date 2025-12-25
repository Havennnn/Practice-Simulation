<?php

namespace App\Services\Reward;

use App\Models\Reward;
use App\Models\UploadedFile;
use App\DataTransferObject\RewardDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class RewardService
{
    public function index(int $page): LengthAwarePaginator
    {
        return Reward::latest()
            ->with('logoFile')
            ->paginate($page)
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
