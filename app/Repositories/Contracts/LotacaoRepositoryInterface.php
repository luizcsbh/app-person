<?php

namespace App\Repositories\Contracts;

use App\Models\Lotacao;

interface LotacaoRepositoryInterface 
{
    public function paginateWithRelations(int $perPage = 10, array $relations = []);

    public function findByIdWithRelations(int $id, array $relations = []);

    public function findActiveCapacity(int $pesId, int $unidId);

    public function create(array $data);

    public function update(Lotacao $lotacao, array $data);

    public function delete(int $id);

}
