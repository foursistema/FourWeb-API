<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTOs\Periodo;
use App\Domain\Dashboard\DTOs\PontoMensal;
use App\Domain\Dashboard\DTOs\TotaisPorCategoria;

interface RendimentoRepositoryInterface
{
    public function totaisPorCategoria(Periodo $periodo): TotaisPorCategoria;

    /** @return list<PontoMensal> */
    public function serieMensal(Periodo $periodo): array;

    public function ultimaAtualizacao(Periodo $periodo): ?\DateTimeImmutable;
}
