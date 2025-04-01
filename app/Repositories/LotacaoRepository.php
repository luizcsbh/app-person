<?php

namespace App\Repositories;

use App\Models\Lotacao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\LotacaoRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class LotacaoRepository
 * 
 * Repositório para operações de banco de dados relacionadas a lotações
 * 
 * @package App\Repositories
 */
class LotacaoRepository implements LotacaoRepositoryInterface
{
    /**
     * @var Lotacao Modelo de Lotação
     */
    private Lotacao $model;

    /**
     * Construtor do repositório
     * 
     * @param Lotacao $model Instância do modelo Lotação
     */
    public function __construct(Lotacao $model)
    {
        $this->model = $model;
    }

    /**
     * Retorna lotações paginadas com relacionamentos
     * 
     * @param int $perPage Número de itens por página (padrão: 10)
     * @param array $relations Relacionamentos para carregar
     * @return LengthAwarePaginator<Lotacao> Resultado paginado com lotações
     */
    public function paginateWithRelations(int $perPage = 10, array $relations = []): LengthAwarePaginator
    {
        return $this->model
            ->with($relations)
            ->paginate($perPage);
    }

    /**
     * Busca uma lotação específica por ID com seus relacionamentos
     * 
     * @param int $id ID da lotação a ser buscada
     * @param array $relations Relacionamentos para carregar
     * @return Lotacao|null A lotação encontrada ou null se não existir
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Lotacao
    {
        return $this->model
            ->with($relations)
            ->find($id);
    }

    /**
     * Verifica se existe lotação ativa para a pessoa e unidade especificadas
     * 
     * @param int $pesId ID da pessoa
     * @param int $unidId ID da unidade
     * @return bool True se existe lotação ativa, False caso contrário
     */
    public function findActiveCapacity(int $pesId, int $unidId): bool
    {
        return $this->model
            ->where('pes_id', $pesId)
            ->where('unid_id', $unidId)
            ->whereNull('lot_data_remocao')
            ->exists();
    }

    /**
     * Cria uma nova lotação no banco de dados
     * 
     * @param array<string, mixed> $data Dados da lotação a ser criada
     * @return Lotacao A lotação criada
     * @throws \Illuminate\Database\QueryException Se ocorrer erro na criação
     */
    public function create(array $data): Lotacao
    {
        return $this->model->create($data);
    }

    /**
     * Atualiza os dados de uma lotação existente
     * 
     * @param array<string, mixed> $data Dados atualizados da lotação
     * @return bool True se a atualização foi bem sucedida, False caso contrário
     * @throws \Illuminate\Database\QueryException Se ocorrer erro na atualização
     */
    public function update(Lotacao $lotacao, array $data)
    {
        try {
            return $lotacao->update($data);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar unidade: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove uma lotação do banco de dados
     * 
     * @param int $id ID da lotação a ser removida
     * @return bool True se a remoção foi bem sucedida, False caso contrário
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se a lotação não for encontrada
     * @throws \Illuminate\Database\QueryException Se ocorrer erro na remoção
     */
    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

}