<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTOs\Filtros;
use App\Domain\Dashboard\DTOs\Periodo;

interface FiltrosRepositoryInterface
{
    public function disponiveis(Periodo $periodo): Filtros;
}
