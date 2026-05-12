<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Dashboard;

use App\Domain\Dashboard\Services\PainelGeralService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PainelGeralRequest;
use App\Http\Resources\Dashboard\PainelGeralResource;

final class GeralController extends Controller
{
    public function __construct(private readonly PainelGeralService $service) {}

    public function __invoke(PainelGeralRequest $request): PainelGeralResource
    {
        $painel = $this->service->build($request->toPeriodo());

        return new PainelGeralResource($painel);
    }
}
