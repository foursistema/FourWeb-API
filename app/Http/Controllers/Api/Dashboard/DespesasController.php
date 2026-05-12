<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Dashboard;

use App\Domain\Dashboard\Services\PainelDespesasService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PainelDespesasRequest;
use App\Http\Resources\Dashboard\PainelDespesasResource;

final class DespesasController extends Controller
{
    public function __construct(private readonly PainelDespesasService $service) {}

    public function __invoke(PainelDespesasRequest $request): PainelDespesasResource
    {
        $painel = $this->service->build($request->toFiltros());

        return new PainelDespesasResource($painel);
    }
}
