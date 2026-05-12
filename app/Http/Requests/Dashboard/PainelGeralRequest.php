<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\Domain\Dashboard\DTOs\Periodo;
use Illuminate\Foundation\Http\FormRequest;

final class PainelGeralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'escola_id' => ['required', 'integer', 'min:1'],
            'ano' => ['nullable', 'integer', 'between:2000,2100'],
            'programa' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function toPeriodo(): Periodo
    {
        return new Periodo(
            escolaId: (int) $this->validated('escola_id'),
            ano: (int) ($this->validated('ano') ?? (int) date('Y')),
            programa: (string) ($this->validated('programa') ?? Periodo::PROGRAMA_PADRAO),
        );
    }
}
