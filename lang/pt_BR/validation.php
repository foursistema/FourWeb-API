<?php

declare(strict_types=1);

return [
    'accepted'             => 'O campo :attribute deve ser aceito.',
    'array'                => 'O campo :attribute precisa ser um array.',
    'between'              => [
        'array'   => 'O campo :attribute deve conter entre :min e :max itens.',
        'file'    => 'O arquivo :attribute deve ter entre :min e :max kilobytes.',
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
        'string'  => 'O campo :attribute deve ter entre :min e :max caracteres.',
    ],
    'boolean'              => 'O campo :attribute deve ser verdadeiro ou falso.',
    'date'                 => 'O campo :attribute não é uma data válida.',
    'email'                => 'O campo :attribute deve ser um e-mail válido.',
    'in'                   => 'O valor selecionado para :attribute é inválido.',
    'integer'              => 'O campo :attribute deve ser um número inteiro.',
    'max'                  => [
        'array'   => 'O campo :attribute não pode ter mais que :max itens.',
        'file'    => 'O arquivo :attribute não pode ser maior que :max kilobytes.',
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'string'  => 'O campo :attribute não pode ter mais que :max caracteres.',
    ],
    'min'                  => [
        'array'   => 'O campo :attribute deve ter no mínimo :min itens.',
        'file'    => 'O arquivo :attribute deve ter no mínimo :min kilobytes.',
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'string'  => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],
    'numeric'              => 'O campo :attribute deve ser um número.',
    'required'             => 'O campo :attribute é obrigatório.',
    'string'               => 'O campo :attribute deve ser uma string.',

    'attributes' => [
        'escola_id' => 'ID da escola',
        'ano'       => 'ano',
        'page'      => 'página',
        'per_page'  => 'itens por página',
        'tipos'     => 'tipos',
        'busca'     => 'busca',
        'empresas'  => 'empresas',
        'naturezas' => 'naturezas',
        'categorias' => 'categorias',
        'meses'     => 'meses',
    ],
];
