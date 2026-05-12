<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

/**
 * Generic categorical slice — used for pie/donut charts where a single
 * category label maps to a single numeric value (e.g. distribuição por natureza).
 */
final readonly class FatiaCategorica
{
    public function __construct(
        public string $rotulo,
        public float $valor,
    ) {}
}
