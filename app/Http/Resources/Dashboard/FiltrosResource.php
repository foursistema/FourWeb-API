<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domain\Dashboard\DTOs\Filtros;
use App\Domain\Dashboard\DTOs\RecursoDisponivel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Filtros $resource
 */
final class FiltrosResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $f = $this->resource;

        return [
            'periodo' => [
                'escola_id' => $f->periodo->escolaId,
                'ano' => $f->periodo->ano,
                'programa' => $f->periodo->programa,
            ],
            'programa_padrao' => $f->programaPadrao,
            'anos_disponiveis' => $f->anosDisponiveis,
            'recursos' => array_map(
                static fn (RecursoDisponivel $r) => [
                    'programa' => $r->programa,
                    'recurso' => $r->recurso,
                ],
                $f->recursos,
            ),
            'empresas' => $f->empresas,
            'naturezas' => $f->naturezas,
            'grupos' => $f->grupos,
            'categorias' => $f->categorias,
        ];
    }
}
