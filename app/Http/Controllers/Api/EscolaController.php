<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Dashboard\Contracts\EscolaRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\EscolaResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EscolaController extends Controller
{
    public function __construct(private readonly EscolaRepositoryInterface $repository) {}

    public function show(int $id): EscolaResource
    {
        $escola = $this->repository->buscarPorId($id);

        if ($escola === null) {
            throw new NotFoundHttpException("Escola {$id} não encontrada.");
        }

        return new EscolaResource($escola);
    }
}
