<?php

namespace App\Repositories\Contracts;

use App\Models\ServidorEfetivo;

interface ServidorEfetivoRepositoryInterface
{
    public function allWithRelations(array $relations = []);

    public function paginateWithRelations(int $perPage = 10, array $relations = []);

    public function findByIdWithRelations(int $id, array $relations = []);

    public function create(ServidorEfetivo $servidor);

    public function update(ServidorEfetivo $servidor, array $data);

    public function delete(ServidorEfetivo $servidor);

    public function findByPessoaId(int $pesId, array $relations = []);

    public function loadRelations(ServidorEfetivo $servidor, array $relations);
}