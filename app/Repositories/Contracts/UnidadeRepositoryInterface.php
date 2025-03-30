<?php

namespace App\Repositories\Contracts;

use App\Models\Unidade;

interface UnidadeRepositoryInterface 
{
    public function allWithRelations(array $relations = []);

    public function paginateWithRelations(int $perPage = 10, array $relations = []);

    public function findByIdWithRelations(int $id, array $relations = []);

    public function create(Unidade $unidade);

    public function update(Unidade $unidade, array $data);

    public function delete(Unidade $unidade);

    public function attachEndereco(Unidade $unidade, int $enderecoId);

    public function getMainEndereco(Unidade $unidade);

    public function loadRelations(Unidade $unidade, array $relations);

    public function unidadeExists(int $unidId);
}
