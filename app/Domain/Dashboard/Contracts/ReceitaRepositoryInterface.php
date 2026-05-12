<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTOs\FatiaCategorica;
use App\Domain\Dashboard\DTOs\OrigemRecurso;
use App\Domain\Dashboard\DTOs\Periodo;
use App\Domain\Dashboard\DTOs\PontoMensal;
use App\Domain\Dashboard\DTOs\TotaisPorCategoria;

interface ReceitaRepositoryInterface
{
    public function totaisPorCategoria(Periodo $periodo): TotaisPorCategoria;

    /** @return list<PontoMensal> */
    public function serieMensal(Periodo $periodo): array;

    public function ultimaAtualizacao(Periodo $periodo): ?\DateTimeImmutable;

    /** @return list<OrigemRecurso> */
    public function origens(Periodo $periodo): array;

    /**
     * Agrega receitas por amparo_legal (motivo legal do repasse), espelhando
     * o clusteredBarChart "ORIGEM RECURSO" da página RECEITAS do Power BI.
     *
     * @return list<FatiaCategorica>
     */
    public function origensPorAmparoLegal(Periodo $periodo): array;
}
