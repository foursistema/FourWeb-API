<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domain\Dashboard\DTOs\FiltrosDespesas;
use App\Domain\Dashboard\DTOs\ListaPaginada;
use App\Domain\Dashboard\DTOs\ServicoDespesa;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{filtros: FiltrosDespesas, pagina: ListaPaginada<ServicoDespesa>} $resource
 */
final class PainelConsultaResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var FiltrosDespesas $f */
        $f = $this->resource['filtros'];
        /** @var ListaPaginada<ServicoDespesa> $p */
        $p = $this->resource['pagina'];

        return [
            'periodo' => [
                'escola_id' => $f->periodo->escolaId,
                'ano' => $f->periodo->ano,
                'programa' => $f->periodo->programa,
            ],
            'filtros_aplicados' => [
                'empresas' => $f->empresas,
                'naturezas' => $f->naturezas,
                'categorias' => $f->categorias,
                'meses' => $f->meses,
            ],
            'page' => $p->page,
            'per_page' => $p->perPage,
            'total' => $p->total,
            'total_pages' => $p->totalPages(),
            'itens' => array_map($this->mapServico(...), $p->itens),
        ];
    }

    /** @return array<string, mixed> */
    private function mapServico(ServicoDespesa $s): array
    {
        return [
            'id' => $s->id,
            'empresa' => $s->empresa,
            'cpf_cnpj_empresa' => $s->cpfCnpjEmpresa,
            'descricao' => $s->descricao,
            'categoria' => $s->categoria,
            'natureza' => $s->natureza,
            'grupo' => $s->grupo,
            'tipo_documento' => $s->tipoDocumento,
            'numero_documento_fiscal' => $s->numeroDocumentoFiscal,
            'data_documento_fiscal' => $s->dataDocumentoFiscal?->format('Y-m-d'),
            'data_pagamento' => $s->dataPagamento?->format('Y-m-d'),
            'quantidade' => $s->quantidade,
            'valor_unidade' => $s->valorUnidade !== null ? round($s->valorUnidade, 2) : null,
            'valor_total_item' => $s->valorTotalItem !== null ? round($s->valorTotalItem, 2) : null,
            'valor_total_documento' => round($s->valorTotalDocumento, 2),
            'forma_pagamento' => $s->formaPagamento,
            'unidade_medida' => $s->unidadeMedida,
            'recurso' => $s->recurso,
            'programa' => $s->programa,
            'observacao' => $s->observacao,
            'numero_mes' => $s->numeroMes,
            'nome_mes' => $s->nomeMes,
        ];
    }
}
