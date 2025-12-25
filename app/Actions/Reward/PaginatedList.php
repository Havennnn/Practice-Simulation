<?php

namespace App\Actions\Reward;

use App\Services\Reward\RewardService;

class PaginatedList
{
    public function __Construct(protected RewardService $service) { }

    public function __invoke(int $perPage  = 25)
    {
        return $this->service->index($perPage);
    }
}
