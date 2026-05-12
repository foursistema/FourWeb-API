<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Contracts\FiltrosRepositoryInterface;
use App\Domain\Dashboard\DTOs\Filtros;
use App\Domain\Dashboard\DTOs\Periodo;
use App\Domain\Dashboard\DTOs\RecursoDisponivel;
use Illuminate\Database\Connection;

final class FiltrosRepository implements FiltrosRepositoryInterface
{
    public function __construct(private readonly Connection $db) {}

    public function disponiveis(Periodo $periodo): Filtros
    {
        return new Filtros(
            periodo: $periodo,
            anosDisponiveis: $this->anosDisponiveis($periodo->escolaId),
            recursos: $this->recursos($periodo),
            empresas: $this->empresas($periodo),
            naturezas: $this->naturezas($periodo),
            grupos: $this->grupos($periodo),
            categorias: ['Capital', 'Custeio'],
            programaPadrao: Periodo::PROGRAMA_PADRAO,
        );
    }

    /** @return list<int> */
    private function anosDisponiveis(int $escolaId): array
    {
        $rows = $this->db->select(<<<'SQL'
            SELECT DISTINCT ano FROM (
                SELECT ano::int AS ano FROM view_tb_receita_all WHERE id_escola::int = ? AND ano IS NOT NULL
                UNION
                SELECT ano FROM view_tb_nf_unica_all WHERE id_escola = ? AND ano IS NOT NULL
                UNION
                SELECT ano FROM view_tb_rendimento_all WHERE id_escola = ? AND ano IS NOT NULL
            ) AS t
            ORDER BY ano DESC
        SQL, [$escolaId, $escolaId, $escolaId]);

        return array_map(static fn ($r) => (int) $r->ano, $rows);
    }

    /**
     * Returns ALL programas the school has activity in for the year — across
     * all 3 fact views. Not filtered by current programa: this is what the
     * RECURSO slicer uses to populate options.
     *
     * @return list<RecursoDisponivel>
     */
    private function recursos(Periodo $periodo): array
    {
        $rows = $this->db->select(<<<'SQL'
            SELECT DISTINCT programa, recurso FROM (
                SELECT programa, recurso FROM view_tb_receita_all
                    WHERE id_escola::int = ? AND ano::int = ? AND programa IS NOT NULL
                UNION
                SELECT programa, recurso FROM view_tb_nf_unica_all
                    WHERE id_escola = ? AND ano = ? AND programa IS NOT NULL
                UNION
                SELECT programa, recurso FROM view_tb_rendimento_all
                    WHERE id_escola = ? AND ano = ? AND programa IS NOT NULL
            ) t
            ORDER BY programa
        SQL, [
            $periodo->escolaId, $periodo->ano,
            $periodo->escolaId, $periodo->ano,
            $periodo->escolaId, $periodo->ano,
        ]);

        return array_map(
            static fn ($r) => new RecursoDisponivel(
                programa: (string) $r->programa,
                recurso: $r->recurso !== null ? (string) $r->recurso : '',
            ),
            $rows,
        );
    }

    /** @return list<string> */
    private function empresas(Periodo $periodo): array
    {
        return $this->distinctNa($periodo, 'view_tb_nf_unica_all', 'empresa');
    }

    /** @return list<string> */
    private function naturezas(Periodo $periodo): array
    {
        return $this->distinctNa($periodo, 'view_tb_nf_unica_all', 'natureza');
    }

    /** @return list<string> */
    private function grupos(Periodo $periodo): array
    {
        return $this->distinctNa($periodo, 'view_tb_nf_all', 'grupo');
    }

    /** @return list<string> */
    private function distinctNa(Periodo $periodo, string $view, string $coluna): array
    {
        $rows = $this->db->select(
            "SELECT DISTINCT {$coluna} FROM {$view} "
            ."WHERE id_escola = ? AND ano = ? AND programa = ? AND {$coluna} IS NOT NULL "
            ."ORDER BY {$coluna}",
            [$periodo->escolaId, $periodo->ano, $periodo->programa],
        );

        return array_map(static fn ($r) => (string) $r->{$coluna}, $rows);
    }
}
