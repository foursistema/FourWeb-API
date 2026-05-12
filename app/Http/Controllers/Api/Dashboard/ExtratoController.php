<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Dashboard;

use App\Domain\Dashboard\Contracts\ExtratoRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PainelExtratoRequest;
use App\Http\Resources\Dashboard\PainelExtratoResource;

final class ExtratoController extends Controller
{
    public function __construct(private readonly ExtratoRepositoryInterface $repository) {}

    public function __invoke(PainelExtratoRequest $request): PainelExtratoResource
    {
        $filtros = $request->toFiltros();
        $pagina = $this->repository->extrato($filtros);

        return new PainelExtratoResource([
            'filtros' => $filtros,
            'pagina' => $pagina,
        ]);
    }
}
