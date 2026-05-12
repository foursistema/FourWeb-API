<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTOs\FiltrosExtrato;
use App\Domain\Dashboard\DTOs\ItemExtrato;
use App\Domain\Dashboard\DTOs\ListaPaginada;

interface ExtratoRepositoryInterface
{
    /** @return ListaPaginada<ItemExtrato> */
    public function extrato(FiltrosExtrato $filtros): ListaPaginada;
}
