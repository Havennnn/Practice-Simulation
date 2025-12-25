<?php

namespace App\Actions\Reward;

use App\Services\Reward\RewardService;

class StoreReward
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected RewardService $service) { }

    public function __invoke($dto) {
        return $this->service->store($dto);
    }
}
