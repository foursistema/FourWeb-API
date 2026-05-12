<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class ItemExtrato
{
    public function __construct(
        public string $id,
        /** One of: receita, despesa, rendimento. */
        public string $tipo,
        public \DateTimeImmutable $data,
        public ?string $descricao,
        public ?string $categoria,
        public float $valor,
        public float $saldoAcumulado,
        public int $numeroMes,
        public string $nomeMes,
    ) {}
}
