<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class Periodo
{
    /** Default mirrors the Power BI slicer's typical selection. */
    public const PROGRAMA_PADRAO = 'PROGEFE';

    public function __construct(
        public int $escolaId,
        public int $ano,
        public string $programa = self::PROGRAMA_PADRAO,
    ) {}
}
