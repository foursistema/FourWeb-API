<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domain\Dashboard\DTOs\PainelGeral;
use App\Domain\Dashboard\DTOs\PontoMensal;
use App\Domain\Dashboard\DTOs\SaldoDisponivel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read PainelGeral $resource
 */
final class PainelGeralResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $p = $this->resource;
        $saldoTotal = $p->saldoTotal();

        return [
            'periodo' => [
                'escola_id' => $p->periodo->escolaId,
                'ano' => $p->periodo->ano,
            ],
            'totais' => [
                'despesa' => $this->totais($p->despesa->capital, $p->despesa->custeio),
                'receita' => $this->totais($p->receita->capital, $p->receita->custeio),
                'rendimento' => $this->totais($p->rendimento->capital, $p->rendimento->custeio),
            ],
            'series_mensais' => [
                'despesa' => array_map($this->mapPonto(...), $p->despesaMensal),
                'receita' => array_map($this->mapPonto(...), $p->receitaMensal),
                'rendimento' => array_map($this->mapPonto(...), $p->rendimentoMensal),
            ],
            'saldo_disponivel' => [
                'capital' => $this->saldo($p->saldoCapital),
                'custeio' => $this->saldo($p->saldoCusteio),
                'total' => $this->saldo($saldoTotal),
            ],
            'meta' => [
                'ultima_atualizacao' => $p->ultimaAtualizacao?->format(\DateTimeInterface::ATOM),
            ],
        ];
    }

    /** @return array<string, float> */
    private function totais(float $capital, float $custeio): array
    {
        return [
            'capital' => round($capital, 2),
            'custeio' => round($custeio, 2),
            'total' => round($capital + $custeio, 2),
        ];
    }

    /** @return array<string, mixed> */
    private function mapPonto(PontoMensal $ponto): array
    {
        return [
            'numero_mes' => $ponto->numeroMes,
            'nome_mes' => $ponto->nomeMes,
            'capital' => round($ponto->capital, 2),
            'custeio' => round($ponto->custeio, 2),
        ];
    }

    /** @return array<string, float> */
    private function saldo(SaldoDisponivel $saldo): array
    {
        return [
            'disponivel' => round($saldo->disponivel, 2),
            'utilizado' => round($saldo->utilizado, 2),
            'percentual_disponivel' => $saldo->percentualDisponivel(),
            'percentual_utilizado' => $saldo->percentualUtilizado(),
        ];
    }
}
