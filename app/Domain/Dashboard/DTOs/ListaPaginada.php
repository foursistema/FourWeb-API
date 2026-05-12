<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

/**
 * @template T of object
 */
final readonly class ListaPaginada
{
    /**
     * @param  list<T>  $itens
     */
    public function __construct(
        public array $itens,
        public int $total,
        public int $page,
        public int $perPage,
    ) {}

    public function totalPages(): int
    {
        return $this->perPage > 0 ? (int) ceil($this->total / $this->perPage) : 0;
    }
}
