<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use App\Domain\Dashboard\Contracts\RendimentoRepositoryInterface;

final class RendimentoRepository extends AbstractFinanceiroRepository implements RendimentoRepositoryInterface
{
    protected function viewName(): string
    {
        return 'view_tb_rendimento_all';
    }

    protected function valueColumn(): string
    {
        return 'rendimento_liquido';
    }

    protected function dataAtualizacaoColumn(): string
    {
        return 'data_rendimento';
    }

    protected function escolaIdIsText(): bool
    {
        return false;
    }

    protected function anoIsText(): bool
    {
        return false;
    }
}
