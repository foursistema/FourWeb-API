<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Services;

use App\Domain\Dashboard\Contracts\DespesaRepositoryInterface;
use App\Domain\Dashboard\Contracts\ReceitaRepositoryInterface;
use App\Domain\Dashboard\Contracts\RendimentoRepositoryInterface;
use App\Domain\Dashboard\DTOs\PainelReceitas;
use App\Domain\Dashboard\DTOs\Periodo;

final readonly class PainelReceitasService
{
    public function __construct(
        private ReceitaRepositoryInterface $receitas,
        private RendimentoRepositoryInterface $rendimentos,
        private DespesaRepositoryInterface $despesas,
        private SaldoCalculator $saldoCalculator,
    ) {}

    public function build(Periodo $periodo): PainelReceitas
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
            $this->rendimentos->ultimaAtualizacao($periodo),
            $this->despesas->ultimaAtualizacao($periodo),
        ]);

        return new PainelReceitas(
            periodo: $periodo,
            receita: $receita,
            rendimento: $rendimento,
            despesa: $despesa,
            receitaMensal: $this->receitas->serieMensal($periodo),
            rendimentoMensal: $this->rendimentos->serieMensal($periodo),
            origens: $this->receitas->origens($periodo),
            origensPorAmparoLegal: $this->receitas->origensPorAmparoLegal($periodo),
            saldoCapital: $saldoCapital,
            saldoCusteio: $saldoCusteio,
            ultimaAtualizacao: empty($datas) ? null : max($datas),
        );
    }
}
