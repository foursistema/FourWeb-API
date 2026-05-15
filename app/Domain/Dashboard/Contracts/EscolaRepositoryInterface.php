<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTOs\Escola;

interface EscolaRepositoryInterface
{
    public function buscarPorId(int $id): ?Escola;

    /**
     * Busca pelo CNPJ normalizado (apenas dígitos). Aceita input com pontos,
     * barras e hífens — normaliza antes de consultar.
     */
    public function buscarPorCnpj(string $cnpj): ?Escola;
}
