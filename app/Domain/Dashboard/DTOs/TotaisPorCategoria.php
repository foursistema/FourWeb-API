<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class TotaisPorCategoria
{
    public function __construct(
        public float $capital,
        public float $custeio,
    ) {}

    public function total(): float
    {
        return $this->capital + $this->custeio;
    }
}
