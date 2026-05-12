<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class PainelGeral
{
    /**
     * @param  list<PontoMensal>  $despesaMensal
     * @param  list<PontoMensal>  $receitaMensal
     * @param  list<PontoMensal>  $rendimentoMensal
     */
    public function __construct(
        public Periodo $periodo,
        public TotaisPorCategoria $despesa,
        public TotaisPorCategoria $receita,
        public TotaisPorCategoria $rendimento,
        public array $despesaMensal,
        public array $receitaMensal,
        public array $rendimentoMensal,
        public SaldoDisponivel $saldoCapital,
        public SaldoDisponivel $saldoCusteio,
        public ?\DateTimeImmutable $ultimaAtualizacao,
    ) {}

    public function saldoTotal(): SaldoDisponivel
    {
        return new SaldoDisponivel(
            disponivel: $this->saldoCapital->disponivel + $this->saldoCusteio->disponivel,
            utilizado: $this->saldoCapital->utilizado + $this->saldoCusteio->utilizado,
        );
    }
}
