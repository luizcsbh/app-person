<?php

namespace App\Repositories\Contracts;

use App\Models\ServidorTemporario;

interface ServidorTemporarioRepositoryInterface
{
    public function allWithRelations(array $relations = []);

    public function paginateWithRelations(int $perPage = 10, array $relations = []);

    public function findByIdWithRelations(int $id, array $relations = []);

    public function create(ServidorTemporario $servidor);

    public function update(ServidorTemporario $servidor, array $data);

    public function delete(ServidorTemporario $servidor);

    public function findByPessoaId(int $pesId, array $relations = []);

    public function loadRelations(ServidorTemporario $servidor, array $relations);
}