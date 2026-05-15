<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Dashboard\Contracts\EscolaRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\EscolaResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EscolaController extends Controller
{
    public function __construct(private readonly EscolaRepositoryInterface $repository) {}

    /** Busca por ID (path param) — retrocompat. */
    public function show(int $id): EscolaResource
    {
        $escola = $this->repository->buscarPorId($id);

        if ($escola === null) {
            throw new NotFoundHttpException("Escola {$id} não encontrada.");
        }

        return new EscolaResource($escola);
    }

    /**
     * Lookup flexível: aceita ?id= ou ?cnpj=. Permite que o SPA seja
     * embedado em sites públicos das escolas usando o CNPJ delas direto
     * na URL (sem precisar saber o ID interno).
     *
     * Exemplos:
     *   GET /api/escola/lookup?id=786
     *   GET /api/escola/lookup?cnpj=03.303.651/0001-49
     *   GET /api/escola/lookup?cnpj=03303651000149
     */
    public function lookup(Request $request): EscolaResource
    {
        $id = $request->query('id');
        $cnpj = $request->query('cnpj');

        if ($id === null && $cnpj === null) {
            throw new NotFoundHttpException('Informe ?id= ou ?cnpj= na querystring.');
        }

        $escola = null;
        if ($id !== null && is_numeric($id)) {
            $escola = $this->repository->buscarPorId((int) $id);
        } elseif (is_string($cnpj) && $cnpj !== '') {
            $escola = $this->repository->buscarPorCnpj($cnpj);
        }

        if ($escola === null) {
            $criterio = $id !== null ? "id={$id}" : "cnpj={$cnpj}";
            throw new NotFoundHttpException("Escola não encontrada para {$criterio}.");
        }

        return new EscolaResource($escola);
    }
}
