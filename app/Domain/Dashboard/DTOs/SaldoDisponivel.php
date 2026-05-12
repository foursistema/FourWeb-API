<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class SaldoDisponivel
{
    public function __construct(
        public float $disponivel,
        public float $utilizado,
    ) {}

    public function bruto(): float
    {
        return $this->disponivel + $this->utilizado;
    }

    public function percentualDisponivel(): float
    {
        $bruto = $this->bruto();
        return $bruto > 0 ? round(($this->disponivel / $bruto) * 100, 2) : 0.0;
    }

    public function percentualUtilizado(): float
    {
        return round(100 - $this->percentualDisponivel(), 2);
    }
}
