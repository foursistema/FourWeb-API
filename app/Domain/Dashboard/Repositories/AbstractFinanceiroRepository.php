<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\DTOs\Periodo;
use App\Domain\Dashboard\DTOs\PontoMensal;
use App\Domain\Dashboard\DTOs\TotaisPorCategoria;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

/**
 * Base for read-only repositories over Power BI's unified views.
 *
 * Each concrete repo declares which view to read, which column holds the
 * monetary value, which column holds the date used for "última atualização",
 * and whether id_escola / ano need ::int cast (some views store them as text).
 *
 * The aggregation primitives — totaisOn() and serieMensalOn() — take an
 * already-scoped Builder so subclasses can append extra filters before
 * computing totals.
 */
abstract class AbstractFinanceiroRepository
{
    public function __construct(protected readonly Connection $db) {}

    abstract protected function viewName(): string;

    abstract protected function valueColumn(): string;

    abstract protected function dataAtualizacaoColumn(): string;

    abstract protected function escolaIdIsText(): bool;

    abstract protected function anoIsText(): bool;

    public function totaisPorCategoria(Periodo $periodo): TotaisPorCategoria
    {
        return $this->totaisOn($this->scopedQuery($periodo));
    }

    /** @return list<PontoMensal> */
    public function serieMensal(Periodo $periodo): array
    {
        return $this->serieMensalOn($this->scopedQuery($periodo));
    }

    public function ultimaAtualizacao(Periodo $periodo): ?\DateTimeImmutable
    {
        $value = $this->scopedQuery($periodo)->max($this->dataAtualizacaoColumn());

        return $value ? new \DateTimeImmutable((string) $value) : null;
    }

    protected function totaisOn(Builder $query): TotaisPorCategoria
    {
        $row = (clone $query)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN categoria = 'Capital' THEN {$this->valueColumn()} ELSE 0 END), 0) as capital,
                COALESCE(SUM(CASE WHEN categoria = 'Custeio' THEN {$this->valueColumn()} ELSE 0 END), 0) as custeio
            ")
            ->first();

        return new TotaisPorCategoria(
            capital: (float) ($row->capital ?? 0),
            custeio: (float) ($row->custeio ?? 0),
        );
    }

    /** @return list<PontoMensal> */
    protected function serieMensalOn(Builder $query): array
    {
        $rows = (clone $query)
            ->selectRaw("
                numero_do_mes,
                nome_do_mes,
                COALESCE(SUM(CASE WHEN categoria = 'Capital' THEN {$this->valueColumn()} ELSE 0 END), 0) as capital,
                COALESCE(SUM(CASE WHEN categoria = 'Custeio' THEN {$this->valueColumn()} ELSE 0 END), 0) as custeio
            ")
            ->whereNotNull('numero_do_mes')
            ->groupBy('numero_do_mes', 'nome_do_mes')
            ->orderBy('numero_do_mes')
            ->get();

        return $rows->map(fn ($r) => new PontoMensal(
            numeroMes: (int) $r->numero_do_mes,
            nomeMes: trim((string) $r->nome_do_mes),
            capital: (float) $r->capital,
            custeio: (float) $r->custeio,
        ))->all();
    }

    protected function scopedQuery(Periodo $periodo): Builder
    {
        $escolaExpr = $this->escolaIdIsText() ? 'id_escola::int' : 'id_escola';
        $anoExpr = $this->anoIsText() ? 'ano::int' : 'ano';

        return $this->db->table($this->viewName())
            ->whereRaw("{$escolaExpr} = ?", [$periodo->escolaId])
            ->whereRaw("{$anoExpr} = ?", [$periodo->ano])
            ->where('programa', $periodo->programa);
    }
}
