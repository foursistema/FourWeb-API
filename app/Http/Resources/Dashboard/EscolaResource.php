<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use App\Domain\Dashboard\DTOs\Escola;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Escola $resource
 */
final class EscolaResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $e = $this->resource;

        return [
            'id' => $e->id,
            'razao_social' => $e->razaoSocial,
            'nome_escola' => $e->nomeEscola,
            'cnpj' => $e->cnpj,
            'diretor' => $e->diretor,
            'municipio' => $e->municipio,
            'codigo_inep' => $e->codigoInep,
            'telefone' => $e->telefone,
            'email' => $e->email,
        ];
    }
}
