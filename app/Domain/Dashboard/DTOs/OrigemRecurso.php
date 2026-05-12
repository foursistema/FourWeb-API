<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class OrigemRecurso
{
    public function __construct(
        public int $id,
        public string $programa,
        public ?string $recurso,
        public ?string $natureza,
        public string $categoria,
        public string $nomeMes,
        public int $numeroMes,
        public float $valor,
        public ?string $observacao,
    ) {}
}
