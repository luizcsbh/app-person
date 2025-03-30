<?php

namespace App\Repositories;

use App\Models\ServidorTemporario;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\ServidorTemporarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServidorTemporarioRepository implements ServidorTemporarioRepositoryInterface
{
    /**
     * @param ServidorTemporario $model Instância do modelo ServidorTemporario
     */
    public function __construct(private ServidorTemporario $model)
    {}

    /**
     * Retorna todos os servidores temporarios com relacionamentos
     * 
     * @param array $relations Relacionamentos para carregar
     * @return Collection Coleção de servidores temporarios
     */
    public function allWithRelations(array $relations = []): Collection
    {
        return $this->model->with($relations)->orderBy('id')->get();
    }

    /**
     * Retorna servidores temporarios paginados com relacionamentos
     * 
     * @param int $perPage Número de itens por página
     * @param array $relations Relacionamentos para carregar
     * @return LengthAwarePaginator Resultado paginado
     */
    public function paginateWithRelations(int $perPage = 10, array $relations = []): LengthAwarePaginator
    {
        return $this->model
            ->with($relations)
            ->paginate($perPage);
    }

    /**
     * Encontra um servidor temporario pelo ID com relacionamentos
     * 
     * @param int $id ID do servidor temporario
     * @param array $relations Relacionamentos para carregar
     * @return ServidorTemporario|null Servidor encontrado ou null
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?ServidorTemporario
    {
        return $this->model
            ->with($relations)
            ->find($id);
    }

    /**
     * Cria um novo registro de servidor temporario
     * 
     * @param ServidorTemporario $servidor Instância do servidor a ser criado
     * @return ServidorTemporario Servidor criado com dados atualizados
     * @throws \RuntimeException Se falhar na criação
     */
    public function create(ServidorTemporario $servidor): ServidorTemporario
    {
        if (!$servidor->save()) {
            throw new \RuntimeException('Falha ao criar servidor temporario');
        }
        return $servidor->refresh();
    }

    /**
     * Atualiza os dados de um servidor temporario
     * 
     * @param ServidorTemporario $servidor Instância do servidor a ser atualizado
     * @param array $data Dados para atualização
     * @return bool True se a atualização foi bem sucedida
     */
    public function update(ServidorTemporario $servidor, array $data): bool
    {
        try {
            return $servidor->update($data);
       } catch (\Exception $e) {
            Log::error('Erro ao atualizar servidor temporário: ' . $e->getMessage());
            return false;
       }
    }

    /**
     * Exclui um registro de servidor temporario
     * 
     * @param ServidorTemporario $servidor Instância do servidor a ser excluído
     * @return bool True se a exclusão foi bem sucedida
     * @throws \RuntimeException Se ocorrer erro na exclusão
     */
    public function delete(ServidorTemporario $servidor): bool
    {
        try {
            return $servidor->delete();
        } catch (\Exception $e) {
            Log::error('Falha ao excluir servidor temporario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Encontra um servidor temporario pelo ID da pessoa relacionada
     * 
     * @param int $pesId ID da pessoa
     * @param array $relations Relacionamentos para carregar
     * @return ServidorTemporario|null Servidor encontrado ou null
     */
    public function findByPessoaId(int $pesId, array $relations = []): ?ServidorTemporario
    {
        return $this->model
            ->with($relations)
            ->where('pes_id', $pesId)
            ->first();
    }


    /**
     * Carrega relacionamentos em uma instância de servidor temporario
     * 
     * @param ServidorTemporario $servidor Instância do servidor
     * @param array $relations Relacionamentos para carregar
     * @return ServidorTemporario Servidor com relacionamentos carregados
     */
    public function loadRelations(ServidorTemporario $servidor, array $relations): ServidorTemporario
    {
        return $servidor->load($relations);
    }
}