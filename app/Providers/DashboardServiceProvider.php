<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Dashboard\Contracts\ConsultaRepositoryInterface;
use App\Domain\Dashboard\Contracts\DespesaRepositoryInterface;
use App\Domain\Dashboard\Contracts\EscolaRepositoryInterface;
use App\Domain\Dashboard\Contracts\ExtratoRepositoryInterface;
use App\Domain\Dashboard\Contracts\FiltrosRepositoryInterface;
use App\Domain\Dashboard\Contracts\ReceitaRepositoryInterface;
use App\Domain\Dashboard\Contracts\RendimentoRepositoryInterface;
use App\Domain\Dashboard\Repositories\ConsultaRepository;
use App\Domain\Dashboard\Repositories\DespesaRepository;
use App\Domain\Dashboard\Repositories\EscolaRepository;
use App\Domain\Dashboard\Repositories\ExtratoRepository;
use App\Domain\Dashboard\Repositories\FiltrosRepository;
use App\Domain\Dashboard\Repositories\ReceitaRepository;
use App\Domain\Dashboard\Repositories\RendimentoRepository;
use Illuminate\Support\ServiceProvider;

final class DashboardServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    private const REPOSITORIES = [
        ReceitaRepositoryInterface::class => ReceitaRepository::class,
        DespesaRepositoryInterface::class => DespesaRepository::class,
        RendimentoRepositoryInterface::class => RendimentoRepository::class,
        FiltrosRepositoryInterface::class => FiltrosRepository::class,
        ConsultaRepositoryInterface::class => ConsultaRepository::class,
        ExtratoRepositoryInterface::class => ExtratoRepository::class,
        EscolaRepositoryInterface::class => EscolaRepository::class,
    ];

    public function register(): void
    {
        foreach (self::REPOSITORIES as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }
}
