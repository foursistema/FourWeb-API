<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Dashboard;

use App\Domain\Dashboard\Contracts\ConsultaRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PainelDespesasRequest;
use App\Http\Resources\Dashboard\PainelConsultaResource;

final class ConsultaController extends Controller
{
    public function __construct(private readonly ConsultaRepositoryInterface $repository) {}

    public function __invoke(PainelDespesasRequest $request): PainelConsultaResource
    {
        $filtros = $request->toFiltros();
        $pagina = $this->repository->listarNotasFiscais($filtros);

        return new PainelConsultaResource([
            'filtros' => $filtros,
            'pagina' => $pagina,
        ]);
    }
}
