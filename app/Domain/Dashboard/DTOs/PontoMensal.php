<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class PontoMensal
{
    public function __construct(
        public int $numeroMes,
        public string $nomeMes,
        public float $capital,
        public float $custeio,
    ) {}
}
