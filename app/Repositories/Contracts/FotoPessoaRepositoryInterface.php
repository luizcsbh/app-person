<?php

namespace App\Repositories\Contracts;

use App\Models\FotoPessoa;

interface FotoPessoaRepositoryInterface 
{
    public function criar(array $dados): FotoPessoa;
    public function buscarPorHash(string $hash): ?FotoPessoa;
    public function buscarPorPessoa(int $pesId): ?FotoPessoa;

}
