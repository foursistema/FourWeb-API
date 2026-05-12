<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class FiltrosExtrato
{
    /** Allowed values for $tipos: 'receita', 'despesa', 'rendimento'. */
    public const TIPOS_VALIDOS = ['receita', 'despesa', 'rendimento'];

    /**
     * @param  list<string>  $tipos
     */
    public function __construct(
        public Periodo $periodo,
        public array $tipos = [],
        public ?string $busca = null,
        public int $page = 1,
        public int $perPage = 50,
    ) {}
}
