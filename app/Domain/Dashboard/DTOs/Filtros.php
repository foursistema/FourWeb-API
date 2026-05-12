<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class Filtros
{
    /**
     * @param  list<int>                  $anosDisponiveis
     * @param  list<RecursoDisponivel>    $recursos
     * @param  list<string>               $empresas
     * @param  list<string>               $naturezas
     * @param  list<string>               $grupos
     * @param  list<string>               $categorias
     */
    public function __construct(
        public Periodo $periodo,
        public array $anosDisponiveis,
        public array $recursos,
        public array $empresas,
        public array $naturezas,
        public array $grupos,
        public array $categorias,
        public string $programaPadrao,
    ) {}
}
