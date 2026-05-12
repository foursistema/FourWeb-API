<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTOs\Escola;

interface EscolaRepositoryInterface
{
    public function buscarPorId(int $id): ?Escola;
}
