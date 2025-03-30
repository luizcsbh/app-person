<?php

namespace App\Repositories;

use App\Models\Endereco;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\EnderecoRepositoryInterface;

/**
 * Repositório para operações de banco de dados relacionadas a endereços
 */
class EnderecoRepository implements EnderecoRepositoryInterface
{
    /**
     * @param Endereco $model Instância do modelo Endereco
     */
    public function __construct(private Endereco $model)
    {}

    /**
     * Retorna todos os endereços com relacionamentos
     * 
     * @param array $relations Relacionamentos para carregar
     * @return Collection Coleção de endereços ordenados por ID
     */
    public function allWithRelations(array $relations = []): Collection
    {
        return $this->model
            ->with($relations)
            ->orderBy('end_id')
            ->get();
    }

    /**
     * Retorna endereços paginados com relacionamentos
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
     * Encontra um endereço por ID com relacionamentos
     * 
     * @param int $id ID do endereço
     * @param array $relations Relacionamentos para carregar
     * @return Endereco|null Endereço encontrado ou null
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Endereco
    {
        return $this->model
            ->with($relations)
            ->find($id);
    }

    /**
     * Cria um novo endereço no banco de dados
     * 
     * @param Endereco $endereco Instância do endereço a ser criado
     * @return Endereco Endereço criado com dados atualizados
     * @throws \RuntimeException Se falhar na criação
     */
    public function create(Endereco $endereco): Endereco
    {
        if (!$endereco->save()) {
            throw new \RuntimeException('Falha ao criar endereço');
        }
        return $endereco->refresh();
    }

    /**
     * Atualiza os dados de um endereço existente
     * 
     * @param Endereco $endereco Instância do endereço a ser atualizado
     * @param array $data Dados para atualização
     * @return bool True se a atualização foi bem sucedida
     */
    public function update(Endereco $endereco, array $data): bool
    {
        return $endereco->update($data);
    }

    /**
     * Exclui um endereço do banco de dados
     * 
     * @param Endereco $endereco Instância do endereço a ser excluído
     * @return bool True se a exclusão foi bem sucedida
     * @throws \RuntimeException Se ocorrer erro na exclusão
     */
    public function delete(Endereco $endereco): bool
    {
        try {
            return $endereco->delete();
        } catch (\Exception $e) {
            throw new \RuntimeException('Falha ao excluir endereço: ' . $e->getMessage());
        }
    }

    /**
     * Carrega relacionamentos em uma instância de endereço
     * 
     * @param Endereco $endereco Endereço para carregar relacionamentos
     * @param array $relations Relacionamentos para carregar
     * @return Endereco Endereço com relacionamentos carregados
     */
    public function loadRelations(Endereco $endereco, array $relations): Endereco
    {
        return $endereco->load($relations);
    }

    /**
     * Encontra endereços pelo ID da cidade relacionada
     * 
     * @param int $cidadeId ID da cidade
     * @param array $relations Relacionamentos para carregar
     * @return Collection Coleção de endereços encontrados
     */
    public function findByCidade(int $cidadeId, array $relations = []): Collection
    {
        return $this->model
            ->with($relations)
            ->where('cid_id', $cidadeId)
            ->get();
    }
}