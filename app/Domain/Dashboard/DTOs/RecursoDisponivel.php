<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class RecursoDisponivel
{
    public function __construct(
        public string $programa,
        public string $recurso,
    ) {}
}
