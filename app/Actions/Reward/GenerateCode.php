<?php

namespace App\Actions\Reward;

use App\Services\Reward\RewardService;

class GenerateCode
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected RewardService $service) { }

    public function __invoke() {
        return $this->service->generateCode();
    }
}
