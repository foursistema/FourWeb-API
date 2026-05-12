<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final readonly class ServicoDespesa
{
    public function __construct(
        public string $id,
        public string $empresa,
        public ?string $cpfCnpjEmpresa,
        public ?string $descricao,
        public ?string $categoria,
        public ?string $natureza,
        public ?string $grupo,
        public ?string $tipoDocumento,
        public ?string $numeroDocumentoFiscal,
        public ?\DateTimeImmutable $dataDocumentoFiscal,
        public ?\DateTimeImmutable $dataPagamento,
        public ?float $quantidade,
        public ?float $valorUnidade,
        public ?float $valorTotalItem,
        public float $valorTotalDocumento,
        public ?string $formaPagamento,
        public ?string $unidadeMedida,
        public ?string $recurso,
        public ?string $programa,
        public ?string $observacao,
        public int $numeroMes,
        public string $nomeMes,
    ) {}
}
