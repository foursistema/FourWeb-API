<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Contracts\ConsultaRepositoryInterface;
use App\Domain\Dashboard\DTOs\FiltrosDespesas;
use App\Domain\Dashboard\DTOs\ListaPaginada;
use App\Domain\Dashboard\DTOs\ServicoDespesa;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

final class ConsultaRepository implements ConsultaRepositoryInterface
{
    public function __construct(private readonly Connection $db) {}

    /** @return ListaPaginada<ServicoDespesa> */
    public function listarNotasFiscais(FiltrosDespesas $filtros): ListaPaginada
    {
        $base = $this->query($filtros);

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

    private function query(FiltrosDespesas $filtros): Builder
    {
        $query = $this->db->table('view_tb_nf_all')
            ->where('id_escola', $filtros->periodo->escolaId)
            ->where('ano', $filtros->periodo->ano)
            ->where('programa', $filtros->periodo->programa);

        if (! empty($filtros->empresas))   $query->whereIn('empresa', $filtros->empresas);
        if (! empty($filtros->naturezas))  $query->whereIn('natureza', $filtros->naturezas);
        if (! empty($filtros->categorias)) $query->whereIn('categoria', $filtros->categorias);
        if (! empty($filtros->meses))      $query->whereIn('numero_do_mes', $filtros->meses);

        return $query;
    }
}
