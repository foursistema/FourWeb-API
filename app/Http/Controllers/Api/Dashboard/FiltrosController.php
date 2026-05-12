<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Dashboard;

use App\Domain\Dashboard\Contracts\FiltrosRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PainelGeralRequest;
use App\Http\Resources\Dashboard\FiltrosResource;

final class FiltrosController extends Controller
{
    public function __construct(private readonly FiltrosRepositoryInterface $repository) {}

    public function __invoke(PainelGeralRequest $request): FiltrosResource
    {
        $filtros = $this->repository->disponiveis($request->toPeriodo());

        return new FiltrosResource($filtros);
    }
}
