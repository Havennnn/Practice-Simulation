<?php

namespace App\Http\Controllers\Admin\Contents;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Actions\Reward\StoreReward;
use App\Actions\Reward\GenerateCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\RewardRequest;
use App\Actions\Reward\PaginatedList;
use App\DataTransferObject\RewardDTO;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\Reward\IndexResource;

class RewardController extends Controller
{
    public function index(Request $request, PaginatedList $list)
    {
        return Inertia::render(
            'Rewards/Index',
            [
                'rewards' => IndexResource::collection($list(
                    perPage: $request->get('perPage', 25)
                )),
            ]
        );
    }

    public function create(): Response
    {
        return Inertia::render('Rewards/Create');
    }

    public function store(RewardRequest $request, StoreReward $store): RedirectResponse
    {
        $store(RewardDTO::fromRequest($request->validated()));

        return redirect(
            route(
                'rewards.index'
            )
        );
    }
    public function generateCode(): JsonResponse
    {
        return response()->json([
            'code' => app(GenerateCode::class)(),
        ]);
    }
}
