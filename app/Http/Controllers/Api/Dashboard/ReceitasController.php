<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Dashboard;

use App\Domain\Dashboard\Services\PainelReceitasService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PainelGeralRequest;
use App\Http\Resources\Dashboard\PainelReceitasResource;

final class ReceitasController extends Controller
{
    public function __construct(private readonly PainelReceitasService $service) {}

    public function __invoke(PainelGeralRequest $request): PainelReceitasResource
    {
        $painel = $this->service->build($request->toPeriodo());

        return new PainelReceitasResource($painel);
    }
}
