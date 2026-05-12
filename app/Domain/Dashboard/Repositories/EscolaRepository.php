<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Contracts\EscolaRepositoryInterface;
use App\Domain\Dashboard\DTOs\Escola;
use Illuminate\Database\Connection;

final class EscolaRepository implements EscolaRepositoryInterface
{
    public function __construct(private readonly Connection $db) {}

    public function buscarPorId(int $id): ?Escola
    {
        $row = $this->db->selectOne(<<<'SQL'
            SELECT id, razao_social, nome_escola, cnpj, diretor, municipio,
                   codigo_inep, telefone, email
            FROM escolas_favoritas
            WHERE id = ?
            LIMIT 1
        SQL, [$id]);

        if ($row === null) {
            return null;
        }

        return new Escola(
            id: (int) $row->id,
            razaoSocial: (string) ($row->razao_social ?? ''),
            nomeEscola: $row->nome_escola !== null ? (string) $row->nome_escola : null,
            cnpj: $row->cnpj !== null ? (string) $row->cnpj : null,
            diretor: $row->diretor !== null ? (string) $row->diretor : null,
            municipio: $row->municipio !== null ? (string) $row->municipio : null,
            codigoInep: $row->codigo_inep !== null ? (string) $row->codigo_inep : null,
            telefone: $row->telefone !== null ? (string) $row->telefone : null,
            email: $row->email !== null ? (string) $row->email : null,
        );
    }
}
