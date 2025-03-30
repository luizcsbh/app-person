<?php

namespace App\Repositories\Contracts;

use App\Models\FotoPessoa;

interface FotoPessoaRepositoryInterface 
{
    public function storeFoto(array $data, $file);

    public function create(array $data);

    public function getFotoById(string $id);

    public function deleteFoto(string $id);

}
