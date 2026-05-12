<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Services;

use App\Domain\Dashboard\Contracts\DespesaRepositoryInterface;
use App\Domain\Dashboard\Contracts\ReceitaRepositoryInterface;
use App\Domain\Dashboard\Contracts\RendimentoRepositoryInterface;
use App\Domain\Dashboard\DTOs\PainelGeral;
use App\Domain\Dashboard\DTOs\Periodo;

final readonly class PainelGeralService
{
    public function __construct(
        private ReceitaRepositoryInterface $receitas,
        private DespesaRepositoryInterface $despesas,
        private RendimentoRepositoryInterface $rendimentos,
        private SaldoCalculator $saldoCalculator,
    ) {}

    public function build(Periodo $periodo): PainelGeral
    {
        $receita = $this->receitas->totaisPorCategoria($periodo);
        $despesa = $this->despesas->totaisPorCategoria($periodo);
        $rendimento = $this->rendimentos->totaisPorCategoria($periodo);

        $saldoCapital = $this->saldoCalculator->calcular(
            entrada: $receita->capital + $rendimento->capital,
            saida: $despesa->capital,
        );

        $saldoCusteio = $this->saldoCalculator->calcular(
            entrada: $receita->custeio + $rendimento->custeio,
            saida: $despesa->custeio,
        );

        $datas = array_filter([
            $this->receitas->ultimaAtualizacao($periodo),
            $this->despesas->ultimaAtualizacao($periodo),
            $this->rendimentos->ultimaAtualizacao($periodo),
        ]);

        return new PainelGeral(
            periodo: $periodo,
            despesa: $despesa,
            receita: $receita,
            rendimento: $rendimento,
            despesaMensal: $this->despesas->serieMensal($periodo),
            receitaMensal: $this->receitas->serieMensal($periodo),
            rendimentoMensal: $this->rendimentos->serieMensal($periodo),
            saldoCapital: $saldoCapital,
            saldoCusteio: $saldoCusteio,
            ultimaAtualizacao: empty($datas) ? null : max($datas),
        );
    }
}
