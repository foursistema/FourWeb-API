<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Contracts\ExtratoRepositoryInterface;
use App\Domain\Dashboard\DTOs\FiltrosExtrato;
use App\Domain\Dashboard\DTOs\ItemExtrato;
use App\Domain\Dashboard\DTOs\ListaPaginada;
use App\Domain\Dashboard\DTOs\Periodo;
use Illuminate\Database\Connection;

final class ExtratoRepository implements ExtratoRepositoryInterface
{
    public function __construct(private readonly Connection $db) {}

    /** @return ListaPaginada<ItemExtrato> */
    public function extrato(FiltrosExtrato $filtros): ListaPaginada
    {
        [$unionSql, $unionBindings] = $this->buildUnionSql($filtros->periodo);
        [$where, $whereBindings] = $this->buildWhere($filtros);

        // Window function gives us running saldo (receita+rendimento somam,
        // despesa subtrai) and total row count in one round-trip so we can
        // paginate without losing accuracy.
        $sql = <<<SQL
            SELECT *
            FROM (
                SELECT
                    t.*,
                    SUM(CASE WHEN tipo = 'despesa' THEN -valor ELSE valor END)
                        OVER (ORDER BY data, id) AS saldo_acumulado,
                    COUNT(*) OVER () AS total_rows
                FROM ({$unionSql}) t
                {$where}
            ) p
            ORDER BY data, id
            OFFSET ? LIMIT ?
        SQL;

        $offset = max(0, ($filtros->page - 1) * $filtros->perPage);
        $bindings = array_merge($unionBindings, $whereBindings, [$offset, $filtros->perPage]);

        $rows = $this->db->select($sql, $bindings);
        $total = empty($rows) ? $this->countTotal($filtros) : (int) $rows[0]->total_rows;

        $itens = array_map(static fn ($r) => new ItemExtrato(
            id: (string) $r->id,
            tipo: (string) $r->tipo,
            data: new \DateTimeImmutable((string) $r->data),
            descricao: $r->descricao !== null ? (string) $r->descricao : null,
            categoria: $r->categoria !== null ? (string) $r->categoria : null,
            valor: (float) $r->valor,
            saldoAcumulado: (float) $r->saldo_acumulado,
            numeroMes: (int) $r->numero_mes,
            nomeMes: trim((string) $r->nome_mes),
        ), $rows);

        return new ListaPaginada(
            itens: $itens,
            total: $total,
            page: $filtros->page,
            perPage: $filtros->perPage,
        );
    }

    private function countTotal(FiltrosExtrato $filtros): int
    {
        [$unionSql, $unionBindings] = $this->buildUnionSql($filtros->periodo);
        [$where, $whereBindings] = $this->buildWhere($filtros);

        $row = $this->db->selectOne(
            "SELECT COUNT(*) AS c FROM ({$unionSql}) t {$where}",
            array_merge($unionBindings, $whereBindings),
        );

        return (int) ($row->c ?? 0);
    }

    /**
     * @return array{0: string, 1: array<int, scalar>}
     */
    private function buildUnionSql(Periodo $periodo): array
    {
        $sql = <<<'SQL'
            SELECT
                'receita'::text   AS tipo,
                id::text          AS id,
                data_recurso      AS data,
                programa          AS descricao,
                categoria         AS categoria,
                valor_total       AS valor,
                numero_do_mes     AS numero_mes,
                nome_do_mes       AS nome_mes
            FROM view_tb_receita_all
            WHERE id_escola::int = ? AND ano::int = ? AND programa = ? AND data_recurso IS NOT NULL

            UNION ALL

            SELECT
                'despesa'::text,
                id::text,
                COALESCE(data_pagamento, data_documento_fiscal, data_do_cadastro) AS data,
                objeto_gasto,
                categoria,
                valor_total_documento,
                numero_do_mes,
                nome_do_mes
            FROM view_tb_nf_unica_all
            WHERE id_escola = ? AND ano = ? AND programa = ?

            UNION ALL

            SELECT
                'rendimento'::text,
                id::text,
                data_rendimento,
                tipo_de_aplicacao,
                categoria,
                rendimento_liquido,
                numero_do_mes,
                nome_do_mes
            FROM view_tb_rendimento_all
            WHERE id_escola = ? AND ano = ? AND programa = ? AND data_rendimento IS NOT NULL
        SQL;

        $bindings = [
            $periodo->escolaId, $periodo->ano, $periodo->programa,
            $periodo->escolaId, $periodo->ano, $periodo->programa,
            $periodo->escolaId, $periodo->ano, $periodo->programa,
        ];

        return [$sql, $bindings];
    }

    /**
     * @return array{0: string, 1: array<int, scalar>}
     */
    private function buildWhere(FiltrosExtrato $filtros): array
    {
        $clauses = [];
        $bindings = [];

        if (! empty($filtros->tipos)) {
            $placeholders = implode(',', array_fill(0, count($filtros->tipos), '?'));
            $clauses[] = "tipo IN ({$placeholders})";
            $bindings = array_merge($bindings, $filtros->tipos);
        }

        if ($filtros->busca !== null && $filtros->busca !== '') {
            $clauses[] = '(descricao ILIKE ? OR categoria ILIKE ?)';
            $term = '%'.$filtros->busca.'%';
            $bindings[] = $term;
            $bindings[] = $term;
        }

        $where = empty($clauses) ? '' : 'WHERE '.implode(' AND ', $clauses);

        return [$where, $bindings];
    }
}
