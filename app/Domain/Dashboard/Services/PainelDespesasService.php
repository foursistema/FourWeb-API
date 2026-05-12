<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Services;

use App\Domain\Dashboard\Contracts\DespesaRepositoryInterface;
use App\Domain\Dashboard\Contracts\ReceitaRepositoryInterface;
use App\Domain\Dashboard\DTOs\FiltrosDespesas;
use App\Domain\Dashboard\DTOs\PainelDespesas;

final readonly class PainelDespesasService
{
    public function __construct(
        private DespesaRepositoryInterface $despesas,
        private ReceitaRepositoryInterface $receitas,
    ) {}

    public function build(FiltrosDespesas $filtros): PainelDespesas
    {
        return new PainelDespesas(
            filtros: $filtros,
            despesa: $this->despesas->totaisPorCategoriaFiltrado($filtros),
            // Receita total da escola/ano/programa (não respeita os filtros de
            // empresa/natureza/etc — esses são internos de despesa) — necessária
            // pra calcular o % de comprometimento dos gauges.
            receita: $this->receitas->totaisPorCategoria($filtros->periodo),
            despesaMensal: $this->despesas->serieMensalFiltrado($filtros),
            topEmpresas: $this->despesas->topPorColuna($filtros, 'empresa'),
            topGrupos: $this->despesas->topPorColuna($filtros, 'grupo'),
            topNaturezas: $this->despesas->topPorColuna($filtros, 'natureza'),
            servicos: $this->despesas->listarServicos($filtros),
            ultimaAtualizacao: $this->despesas->ultimaAtualizacao($filtros->periodo),
        );
    }
}
