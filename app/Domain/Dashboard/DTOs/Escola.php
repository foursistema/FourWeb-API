<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class Escola
{
    public function __construct(
        public int $id,
        public string $razaoSocial,
        public ?string $nomeEscola,
        public ?string $cnpj,
        public ?string $diretor,
        public ?string $municipio,
        public ?string $codigoInep,
        public ?string $telefone,
        public ?string $email,
    ) {}
}
