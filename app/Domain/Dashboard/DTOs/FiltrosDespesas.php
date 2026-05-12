<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class FiltrosDespesas
{
    /**
     * @param  list<string>  $empresas
     * @param  list<string>  $naturezas
     * @param  list<string>  $categorias
     * @param  list<int>     $meses
     */
    public function __construct(
        public Periodo $periodo,
        public array $empresas = [],
        public array $naturezas = [],
        public array $categorias = [],
        public array $meses = [],
        public int $top = 5,
        public int $page = 1,
        public int $perPage = 50,
    ) {}
}
