<?php

namespace App\Repositories;

use App\Models\FotoPessoa;
use App\Repositories\Contracts\FotoPessoaRepositoryInterface;

class FotoPessoaRepository implements FotoPessoaRepositoryInterface
{
    public function criar(array $dados): FotoPessoa
    {
        return FotoPessoa::create($dados);
    }

    public function buscarPorHash(string $hash): ?FotoPessoa
    {
        return FotoPessoa::where('fp_hash', $hash)->first();
    }

    public function buscarPorPessoa(int $pesId): ?FotoPessoa
    {
        return FotoPessoa::where('pes_id', $pesId)->latest()->first();
    }
}