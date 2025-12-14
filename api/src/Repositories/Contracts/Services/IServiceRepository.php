<?php

namespace App\Repositories\Contracts\Services;

use App\Repositories\Contracts\BaseInterface;

interface IServiceRepository extends BaseInterface
{
    public function findByNome(string $nome);
    public function findByCategoria(string $categoria);
    public function findBySituacao(string $situacao);
    public function findByValorRange(float $minValor, float $maxValor);
    public function findByDuracao(int $duracao);
}
