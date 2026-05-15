<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\Domain\Dashboard\DTOs\FiltrosExtrato;
use App\Domain\Dashboard\DTOs\Periodo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PainelExtratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'escola_id' => ['required', 'integer', 'min:1'],
            'ano'       => ['nullable', 'integer', 'between:2000,2100'],
            'programa'  => ['nullable', 'string', 'max:120'],
            'tipos'     => ['nullable', 'array'],
            'tipos.*'   => ['string', Rule::in(FiltrosExtrato::TIPOS_VALIDOS)],
            'busca'     => ['nullable', 'string', 'max:120'],
            'page'      => ['nullable', 'integer', 'min:1'],
            // Limite alto pra suportar pré-carregamento do extrato inteiro do
            // ano no SPA (drill tooltip dos charts da Home consome tudo de uma vez).
            'per_page'  => ['nullable', 'integer', 'between:1,5000'],
        ];
    }

    public function toFiltros(): FiltrosExtrato
    {
        $periodo = new Periodo(
            escolaId: (int) $this->validated('escola_id'),
            ano: (int) ($this->validated('ano') ?? (int) date('Y')),
            programa: (string) ($this->validated('programa') ?? Periodo::PROGRAMA_PADRAO),
        );

        return new FiltrosExtrato(
            periodo: $periodo,
            tipos:   array_values((array) $this->validated('tipos', [])),
            busca:   $this->validated('busca'),
            page:    (int) ($this->validated('page') ?? 1),
            perPage: (int) ($this->validated('per_page') ?? 50),
        );
    }
}
