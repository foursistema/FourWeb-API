<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domain\Dashboard\DTOs\FiltrosExtrato;
use App\Domain\Dashboard\DTOs\ItemExtrato;
use App\Domain\Dashboard\DTOs\ListaPaginada;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{filtros: FiltrosExtrato, pagina: ListaPaginada} $resource
 */
final class PainelExtratoResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var FiltrosExtrato $f */
        $f = $this->resource['filtros'];
        /** @var ListaPaginada<ItemExtrato> $p */
        $p = $this->resource['pagina'];

        return [
            'periodo' => [
                'escola_id' => $f->periodo->escolaId,
                'ano' => $f->periodo->ano,
                'programa' => $f->periodo->programa,
            ],
            'filtros_aplicados' => [
                'tipos' => $f->tipos,
                'busca' => $f->busca,
            ],
            'page' => $p->page,
            'per_page' => $p->perPage,
            'total' => $p->total,
            'total_pages' => $p->totalPages(),
            'itens' => array_map($this->mapItem(...), $p->itens),
        ];
    }

    /** @return array<string, mixed> */
    private function mapItem(ItemExtrato $i): array
    {
        return [
            'id' => $i->id,
            'tipo' => $i->tipo,
            'data' => $i->data->format('Y-m-d'),
            'descricao' => $i->descricao,
            'categoria' => $i->categoria,
            'valor' => round($i->valor, 2),
            'saldo_acumulado' => round($i->saldoAcumulado, 2),
            'numero_mes' => $i->numeroMes,
            'nome_mes' => $i->nomeMes,
        ];
    }
}
