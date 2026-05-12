<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Contracts\DespesaRepositoryInterface;
use App\Domain\Dashboard\DTOs\FatiaCategorica;
use App\Domain\Dashboard\DTOs\FiltrosDespesas;
use App\Domain\Dashboard\DTOs\ListaPaginada;
use App\Domain\Dashboard\DTOs\PontoMensal;
use App\Domain\Dashboard\DTOs\ServicoDespesa;
use App\Domain\Dashboard\DTOs\TotaisPorCategoria;
use Illuminate\Database\Query\Builder;

final class DespesaRepository extends AbstractFinanceiroRepository implements DespesaRepositoryInterface
{
    public function totaisPorCategoriaFiltrado(FiltrosDespesas $filtros): TotaisPorCategoria
    {
        return $this->totaisOn($this->queryComFiltros($filtros));
    }

    /** @return list<PontoMensal> */
    public function serieMensalFiltrado(FiltrosDespesas $filtros): array
    {
        return $this->serieMensalOn($this->queryComFiltros($filtros));
    }

    /**
     * Roda na view_tb_nf_all (todas as linhas, 1 por item) somando
     * valor_total_item — replica os charts TOP 5 do Power BI que precisam
     * de granularidade por item (a view _unica perde itens secundários).
     *
     * @return list<FatiaCategorica>
     */
    public function topPorColuna(FiltrosDespesas $filtros, string $coluna): array
    {
        // Allowlist defensiva — coluna vai concatenada na SQL.
        $colunasPermitidas = ['empresa', 'grupo', 'natureza', 'categoria', 'recurso'];
        if (! in_array($coluna, $colunasPermitidas, true)) {
            throw new \InvalidArgumentException("Coluna agrupadora inválida: {$coluna}");
        }

        $query = $this->db->table('view_tb_nf_all')
            ->where('id_escola', $filtros->periodo->escolaId)
            ->where('ano', $filtros->periodo->ano)
            ->where('programa', $filtros->periodo->programa);

        if (! empty($filtros->empresas))   $query->whereIn('empresa', $filtros->empresas);
        if (! empty($filtros->naturezas))  $query->whereIn('natureza', $filtros->naturezas);
        if (! empty($filtros->categorias)) $query->whereIn('categoria', $filtros->categorias);
        if (! empty($filtros->meses))      $query->whereIn('numero_do_mes', $filtros->meses);

        $rows = $query
            ->selectRaw("{$coluna} AS rotulo, COALESCE(SUM(valor_total_item), 0) AS valor")
            ->whereNotNull($coluna)
            ->groupBy($coluna)
            ->orderByDesc('valor')
            ->limit($filtros->top)
            ->get();

        return $rows->map(static fn ($r) => new FatiaCategorica(
            rotulo: (string) $r->rotulo,
            valor: (float) $r->valor,
        ))->all();
    }

    /** @return ListaPaginada<ServicoDespesa> */
    public function listarServicos(FiltrosDespesas $filtros): ListaPaginada
    {
        $base = $this->queryComFiltros($filtros);

        $total = (clone $base)->count();

        $offset = max(0, ($filtros->page - 1) * $filtros->perPage);

        $rows = $base
            ->orderBy('numero_do_mes')
            ->orderBy('empresa')
            ->orderBy('id')
            ->offset($offset)
            ->limit($filtros->perPage)
            ->get();

        $itens = $rows->map(static fn ($r) => new ServicoDespesa(
            id: (string) $r->id,
            empresa: (string) ($r->empresa ?? ''),
            cpfCnpjEmpresa: $r->cpf_cnpj_empresa !== null ? (string) $r->cpf_cnpj_empresa : null,
            descricao: $r->objeto_gasto !== null ? (string) $r->objeto_gasto : null,
            categoria: $r->categoria !== null ? (string) $r->categoria : null,
            natureza: $r->natureza !== null ? (string) $r->natureza : null,
            grupo: $r->grupo !== null ? (string) $r->grupo : null,
            tipoDocumento: $r->tipo_de_documento !== null ? (string) $r->tipo_de_documento : null,
            numeroDocumentoFiscal: $r->numero_documento_fiscal !== null ? (string) $r->numero_documento_fiscal : null,
            dataDocumentoFiscal: $r->data_documento_fiscal ? new \DateTimeImmutable((string) $r->data_documento_fiscal) : null,
            dataPagamento: $r->data_pagamento ? new \DateTimeImmutable((string) $r->data_pagamento) : null,
            quantidade: $r->quantidade !== null ? (float) $r->quantidade : null,
            valorUnidade: $r->valor_unidade !== null ? (float) $r->valor_unidade : null,
            valorTotalItem: $r->valor_total_item !== null ? (float) $r->valor_total_item : null,
            valorTotalDocumento: (float) ($r->valor_total_documento ?? 0),
            formaPagamento: $r->forma_de_pagamento !== null ? (string) $r->forma_de_pagamento : null,
            unidadeMedida: $r->unidade_de_medida !== null ? (string) $r->unidade_de_medida : null,
            recurso: $r->recurso !== null ? (string) $r->recurso : null,
            programa: $r->programa !== null ? (string) $r->programa : null,
            observacao: $r->observacao !== null ? (string) $r->observacao : null,
            numeroMes: (int) $r->numero_do_mes,
            nomeMes: trim((string) $r->nome_do_mes),
        ))->all();

        return new ListaPaginada(
            itens: $itens,
            total: $total,
            page: $filtros->page,
            perPage: $filtros->perPage,
        );
    }

    protected function viewName(): string
    {
        return 'view_tb_nf_unica_all';
    }

    protected function valueColumn(): string
    {
        return 'valor_total_documento';
    }

    protected function dataAtualizacaoColumn(): string
    {
        return 'data_do_cadastro';
    }

    protected function escolaIdIsText(): bool
    {
        return false;
    }

    protected function anoIsText(): bool
    {
        return false;
    }

    private function queryComFiltros(FiltrosDespesas $filtros): Builder
    {
        $query = $this->scopedQuery($filtros->periodo);

        if (! empty($filtros->empresas)) {
            $query->whereIn('empresa', $filtros->empresas);
        }
        if (! empty($filtros->naturezas)) {
            $query->whereIn('natureza', $filtros->naturezas);
        }
        if (! empty($filtros->categorias)) {
            $query->whereIn('categoria', $filtros->categorias);
        }
        if (! empty($filtros->meses)) {
            $query->whereIn('numero_do_mes', $filtros->meses);
        }

        return $query;
    }
}
