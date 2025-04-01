<?php

namespace App\Repositories\Contracts;

use App\Models\Pessoa;

interface PessoaRepositoryInterface 
{
    public function allWithRelations(array $relations = []);

    public function paginateWithRelations(int $perPage = 10, array $relations = []);

    public function findByIdWithRelations(int $id, array $relations = []);

    public function create(Pessoa $pessoa);

    public function update(Pessoa $pessoa, array $data);

    public function delete(Pessoa $pessoa);

    public function attachEndereco(Pessoa $pessoa, int $enderecoId);

    public function getMainEndereco(Pessoa $pessoa);

    public function loadRelations(Pessoa $pessoa, array $relations);

    public function buscarServidoresPorUnidade(int $unidId,int $perPage);
}
