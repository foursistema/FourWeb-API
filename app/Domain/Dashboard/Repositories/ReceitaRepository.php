<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Contracts\ReceitaRepositoryInterface;
use App\Domain\Dashboard\DTOs\FatiaCategorica;
use App\Domain\Dashboard\DTOs\OrigemRecurso;
use App\Domain\Dashboard\DTOs\Periodo;

final class ReceitaRepository extends AbstractFinanceiroRepository implements ReceitaRepositoryInterface
{
    public function origens(Periodo $periodo): array
    {
        $rows = $this->scopedQuery($periodo)
            ->select([
                'id', 'programa', 'recurso', 'natureza', 'categoria',
                'nome_do_mes', 'numero_do_mes', 'valor_total', 'observacao',
            ])
            ->orderBy('numero_do_mes')
            ->orderByDesc('valor_total')
            ->get();

        return $rows->map(fn ($r) => new OrigemRecurso(
            id: (int) $r->id,
            programa: (string) ($r->programa ?? ''),
            recurso: $r->recurso !== null ? (string) $r->recurso : null,
            natureza: $r->natureza !== null ? (string) $r->natureza : null,
            categoria: (string) ($r->categoria ?? ''),
            nomeMes: trim((string) $r->nome_do_mes),
            numeroMes: (int) $r->numero_do_mes,
            valor: (float) $r->valor_total,
            observacao: $r->observacao !== null ? (string) $r->observacao : null,
        ))->all();
    }

    /** @return list<FatiaCategorica> */
    public function origensPorAmparoLegal(Periodo $periodo): array
    {
        $rows = $this->scopedQuery($periodo)
            ->selectRaw('amparo_legal, COALESCE(SUM(valor_total), 0) AS valor')
            ->whereNotNull('amparo_legal')
            ->groupBy('amparo_legal')
            ->orderByDesc('valor')
            ->get();

        return $rows->map(static fn ($r) => new FatiaCategorica(
            rotulo: (string) $r->amparo_legal,
            valor: (float) $r->valor,
        ))->all();
    }

    protected function viewName(): string
    {
        return 'view_tb_receita_all';
    }

    protected function valueColumn(): string
    {
        return 'valor_total';
    }

    protected function dataAtualizacaoColumn(): string
    {
        return 'data_do_cadastro';
    }

    protected function escolaIdIsText(): bool
    {
        return true;
    }

    protected function anoIsText(): bool
    {
        return true;
    }
}
