<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class PainelDespesas
{
    /**
     * @param  list<PontoMensal>           $despesaMensal
     * @param  list<FatiaCategorica>       $topEmpresas
     * @param  list<FatiaCategorica>       $topGrupos
     * @param  list<FatiaCategorica>       $topNaturezas
     * @param  ListaPaginada<ServicoDespesa> $servicos
     */
    public function __construct(
        public FiltrosDespesas $filtros,
        public TotaisPorCategoria $despesa,
        public TotaisPorCategoria $receita,
        public array $despesaMensal,
        public array $topEmpresas,
        public array $topGrupos,
        public array $topNaturezas,
        public ListaPaginada $servicos,
        public ?\DateTimeImmutable $ultimaAtualizacao,
    ) {}

    /**
     * Percentuais de comprometimento (despesa/receita) — base dos 3 gauges
     * "PORC_DEPESA_*_GERAL" do Power BI. Retorna em percentual (0-200 etc.).
     */
    public function percentualComprometimentoCapital(): float
    {
        return $this->receita->capital > 0
            ? round(($this->despesa->capital / $this->receita->capital) * 100, 2)
            : 0.0;
    }

    public function percentualComprometimentoCusteio(): float
    {
        return $this->receita->custeio > 0
            ? round(($this->despesa->custeio / $this->receita->custeio) * 100, 2)
            : 0.0;
    }

    public function percentualComprometimentoTotal(): float
    {
        return $this->receita->total() > 0
            ? round(($this->despesa->total() / $this->receita->total()) * 100, 2)
            : 0.0;
    }
}
