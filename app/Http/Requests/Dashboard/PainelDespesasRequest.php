<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\Domain\Dashboard\DTOs\FiltrosDespesas;
use App\Domain\Dashboard\DTOs\Periodo;
use Illuminate\Foundation\Http\FormRequest;

final class PainelDespesasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'escola_id'    => ['required', 'integer', 'min:1'],
            'ano'          => ['nullable', 'integer', 'between:2000,2100'],
            'programa'     => ['nullable', 'string', 'max:120'],
            'empresas'     => ['nullable', 'array'],
            'empresas.*'   => ['string'],
            'naturezas'    => ['nullable', 'array'],
            'naturezas.*'  => ['string'],
            'categorias'   => ['nullable', 'array'],
            'categorias.*' => ['string', 'in:Capital,Custeio'],
            'meses'        => ['nullable', 'array'],
            'meses.*'      => ['integer', 'between:1,12'],
            'top'          => ['nullable', 'integer', 'between:1,50'],
            'page'         => ['nullable', 'integer', 'min:1'],
            'per_page'     => ['nullable', 'integer', 'between:1,200'],
        ];
    }

    public function toFiltros(): FiltrosDespesas
    {
        $periodo = new Periodo(
            escolaId: (int) $this->validated('escola_id'),
            ano: (int) ($this->validated('ano') ?? (int) date('Y')),
            programa: (string) ($this->validated('programa') ?? Periodo::PROGRAMA_PADRAO),
        );

        return new FiltrosDespesas(
            periodo:    $periodo,
            empresas:   array_values((array) $this->validated('empresas', [])),
            naturezas:  array_values((array) $this->validated('naturezas', [])),
            categorias: array_values((array) $this->validated('categorias', [])),
            meses:      array_map('intval', array_values((array) $this->validated('meses', []))),
            top:        (int) ($this->validated('top') ?? 5),
            page:       (int) ($this->validated('page') ?? 1),
            perPage:    (int) ($this->validated('per_page') ?? 50),
        );
    }
}
