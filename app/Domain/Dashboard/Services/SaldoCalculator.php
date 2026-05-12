<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Services;

use App\Domain\Dashboard\DTOs\SaldoDisponivel;

/**
 * Replica a DAX:
 *   RESULT_X_GERAL = Receita X + Rendimento X − Despesa X
 *
 * O resultado é o "saldo disponível" (entrada líquida).
 * "Utilizado" é capped à despesa real para que o par disponível+utilizado
 * sempre some à entrada total — útil pros gráficos de pizza tipo "Disponível
 * vs Utilizado" que o Power BI mostra na página GERAL.
 */
final readonly class SaldoCalculator
{
    public function calcular(float $entrada, float $saida): SaldoDisponivel
    {
        return new SaldoDisponivel(
            disponivel: max(0.0, $entrada - $saida),
            utilizado: min($entrada, $saida),
        );
    }
}
