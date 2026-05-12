<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domain\Dashboard\DTOs\FatiaCategorica;
use App\Domain\Dashboard\DTOs\PainelDespesas;
use App\Domain\Dashboard\DTOs\PontoMensal;
use App\Domain\Dashboard\DTOs\ServicoDespesa;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read PainelDespesas $resource
 */
final class PainelDespesasResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $p = $this->resource;
        $f = $p->filtros;

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
                'top' => $f->top,
            ],
            'totais' => [
                'capital' => round($p->despesa->capital, 2),
                'custeio' => round($p->despesa->custeio, 2),
                'total' => round($p->despesa->total(), 2),
            ],
            // Replica os 3 gauges PORC_DEPESA_*_GERAL do Power BI.
            'comprometimento' => [
                'capital' => $p->percentualComprometimentoCapital(),
                'custeio' => $p->percentualComprometimentoCusteio(),
                'total' => $p->percentualComprometimentoTotal(),
            ],
            'serie_mensal' => array_map($this->mapPonto(...), $p->despesaMensal),
            'top' => [
                'empresas' => array_map($this->mapFatia(...), $p->topEmpresas),
                'grupos' => array_map($this->mapFatia(...), $p->topGrupos),
                'naturezas' => array_map($this->mapFatia(...), $p->topNaturezas),
            ],
            'servicos' => [
                'page' => $p->servicos->page,
                'per_page' => $p->servicos->perPage,
                'total' => $p->servicos->total,
                'total_pages' => $p->servicos->totalPages(),
                'itens' => array_map($this->mapServico(...), $p->servicos->itens),
            ],
            'meta' => [
                'ultima_atualizacao' => $p->ultimaAtualizacao?->format(\DateTimeInterface::ATOM),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function mapPonto(PontoMensal $p): array
    {
        return [
            'numero_mes' => $p->numeroMes,
            'nome_mes' => $p->nomeMes,
            'capital' => round($p->capital, 2),
            'custeio' => round($p->custeio, 2),
        ];
    }

    /** @return array<string, mixed> */
    private function mapFatia(FatiaCategorica $f): array
    {
        return [
            'rotulo' => $f->rotulo,
            'valor' => round($f->valor, 2),
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
