<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTOs\FiltrosDespesas;
use App\Domain\Dashboard\DTOs\ListaPaginada;
use App\Domain\Dashboard\DTOs\ServicoDespesa;

interface ConsultaRepositoryInterface
{
    /**
     * Lista NFs detalhadas (1 linha por item, view_tb_nf_all) com filtros —
     * replica a tabela NOTAS FISCAIS da página CONSULTA DESPESAS do Power BI.
     *
     * @return ListaPaginada<ServicoDespesa>
     */
    public function listarNotasFiscais(FiltrosDespesas $filtros): ListaPaginada;
}
