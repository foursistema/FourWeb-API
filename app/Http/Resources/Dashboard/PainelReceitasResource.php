<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domain\Dashboard\DTOs\FatiaCategorica;
use App\Domain\Dashboard\DTOs\OrigemRecurso;
use App\Domain\Dashboard\DTOs\PainelReceitas;
use App\Domain\Dashboard\DTOs\PontoMensal;
use App\Domain\Dashboard\DTOs\SaldoDisponivel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read PainelReceitas $resource
 */
final class PainelReceitasResource extends JsonResource
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
                'programa' => $p->periodo->programa,
            ],
            'totais' => [
                'receita' => $this->totais($p->receita->capital, $p->receita->custeio),
                'rendimento' => $this->totais($p->rendimento->capital, $p->rendimento->custeio),
            ],
            'series_mensais' => [
                'receita' => array_map($this->mapPonto(...), $p->receitaMensal),
                'rendimento' => array_map($this->mapPonto(...), $p->rendimentoMensal),
            ],
            'origens_por_amparo_legal' => array_map($this->mapFatia(...), $p->origensPorAmparoLegal),
            'origens_detalhadas' => array_map($this->mapOrigem(...), $p->origens),
            'saldo_disponivel' => [
                'capital' => $this->saldo($p->saldoCapital),
                'custeio' => $this->saldo($p->saldoCusteio),
                'total' => $this->saldo($saldoTotal),
            ],
            'meta' => [
                'ultima_atualizacao' => $p->ultimaAtualizacao?->format(\DateTimeInterface::ATOM),
                'total_origens' => count($p->origens),
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
    private function mapPonto(PontoMensal $p): array
    {
        return [
            'numero_mes' => $p->numeroMes,
            'nome_mes' => $p->nomeMes,
            'capital' => round($p->capital, 2),
            'custeio' => round($p->custeio, 2),
        ];
    }

    /** @return array<string, mixed> */
    private function mapOrigem(OrigemRecurso $o): array
    {
        return [
            'id' => $o->id,
            'programa' => $o->programa,
            'recurso' => $o->recurso,
            'natureza' => $o->natureza,
            'categoria' => $o->categoria,
            'numero_mes' => $o->numeroMes,
            'nome_mes' => $o->nomeMes,
            'valor' => round($o->valor, 2),
            'observacao' => $o->observacao,
        ];
    }

    /** @return array<string, mixed> */
    private function mapFatia(FatiaCategorica $f): array
    {
        return [
            'rotulo' => $f->rotulo,
            'valor' => round($f->valor, 2),
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
