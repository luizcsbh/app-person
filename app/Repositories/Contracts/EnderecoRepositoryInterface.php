<?php

namespace App\Repositories\Contracts;

use App\Models\Endereco;

interface EnderecoRepositoryInterface 
{
    public function allWithRelations(array $relations = []);

    public function paginateWithRelations(int $perPage = 10, array $relations = []);

    public function findByIdWithRelations(int $id, array $relations = []);

    public function create(Endereco $endereco);

    public function update(Endereco $endereco, array $data);

    public function delete(Endereco $endereco);

    public function loadRelations(Endereco $endereco, array $relations);

    public function findByCidade(int $cidadeId, array $relations = []);
}
