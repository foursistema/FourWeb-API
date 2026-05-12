<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTOs\FatiaCategorica;
use App\Domain\Dashboard\DTOs\FiltrosDespesas;
use App\Domain\Dashboard\DTOs\ListaPaginada;
use App\Domain\Dashboard\DTOs\Periodo;
use App\Domain\Dashboard\DTOs\PontoMensal;
use App\Domain\Dashboard\DTOs\ServicoDespesa;
use App\Domain\Dashboard\DTOs\TotaisPorCategoria;

interface DespesaRepositoryInterface
{
    public function totaisPorCategoria(Periodo $periodo): TotaisPorCategoria;

    /** @return list<PontoMensal> */
    public function serieMensal(Periodo $periodo): array;

    public function ultimaAtualizacao(Periodo $periodo): ?\DateTimeImmutable;

    public function totaisPorCategoriaFiltrado(FiltrosDespesas $filtros): TotaisPorCategoria;

    /** @return list<PontoMensal> */
    public function serieMensalFiltrado(FiltrosDespesas $filtros): array;

    /**
     * TOP N items por coluna agrupadora (empresa, grupo, natureza, ...),
     * lendo view_tb_nf_all e somando valor_total_item — espelha o cálculo
     * dos clusteredBarChart "TOP 5 *" da página DESPESAS do Power BI.
     *
     * @return list<FatiaCategorica>
     */
    public function topPorColuna(FiltrosDespesas $filtros, string $coluna): array;

    /** @return ListaPaginada<ServicoDespesa> */
    public function listarServicos(FiltrosDespesas $filtros): ListaPaginada;
}
