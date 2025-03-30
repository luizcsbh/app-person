<?php

namespace App\Repositories;

use App\Models\Unidade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\UnidadeRepositoryInterface;
use Illuminate\Support\Facades\Log;

/**
 * Repositório para operações de banco de dados relacionadas a unidades
 */
class UnidadeRepository implements UnidadeRepositoryInterface
{
    /**
     * @param Unidade $model Instância do modelo Unidade
     */
    public function __construct(
        private Unidade $model
    ) {}

    /**
     * Retorna todas as unidades com relacionamentos
     * 
     * @param array $relations Relacionamentos para carregar
     * @return Collection Coleção de unidades com relacionamentos
     */
    public function allWithRelations(array $relations = []): Collection
    {
        return $this->model
            ->with($relations)
            ->orderBy('unid_id')
            ->get();
    }

    /**
     * Retorna unidades paginadas com relacionamentos
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
     * Encontra uma unidade por ID com relacionamentos
     * 
     * @param int $id ID da unidade
     * @param array $relations Relacionamentos para carregar
     * @return Unidade|null Unidade encontrada ou null
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Unidade
    {
        return $this->model
            ->with($relations)
            ->find($id);
    }

    /**
     * Cria uma nova unidade no banco de dados
     * 
     * @param Unidade $unidade Instância da unidade a ser criada
     * @return Unidade Unidade criada com dados atualizados
     * @throws \RuntimeException Se falhar na criação
     */
    public function create(Unidade $unidade): Unidade
    {
        if (!$unidade->save()) {
            throw new \RuntimeException('Falha ao criar registro de unidade');
        }
        return $unidade->refresh(['unid_id']);
    }

    /**
     * Atualiza os dados de uma unidade
     * 
     * @param Unidade $unidade Instância da unidade a ser atualizada
     * @param array $data Dados para atualização
     * @return bool True se a atualização foi bem sucedida
     */
    public function update(Unidade $unidade, array $data): bool
    {
        try {
            return $unidade->update($data);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar unidade: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui uma unidade do banco de dados
     * 
     * @param Unidade $unidade Instância da unidade a ser excluída
     * @return bool True se a exclusão foi bem sucedida
     * @throws \RuntimeException Se ocorrer erro na exclusão
     */
    public function delete(Unidade $unidade): bool
    {
        try {
            return $unidade->delete();
        } catch (\Exception $e) {
            Log::error('Falha ao excluir unidade: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vincula um endereço a uma unidade
     * 
     * @param Unidade $unidade Unidade para vincular o endereço
     * @param int $enderecoId ID do endereço
     * @throws \RuntimeException Se falhar no vínculo
     */
    public function attachEndereco(Unidade $unidade, int $enderecoId): void
    {
        try {
            $unidade->enderecos()->attach($enderecoId, [
                'created_at' => now(),
                'updated_at' => now()
            ]);

        } catch (\Exception $e) {
            throw new \RuntimeException('Falha ao vincular endereço: ' . $e->getMessage());
        }
    }

    /**
     * Obtém o ID do endereço principal de uma unidade
     * 
     * @param Unidade $unidade Unidade para consulta
     * @return int|null ID do endereço mais antigo ou null
     */
    public function getMainEndereco(Unidade $unidade): ?int
    {
        return $unidade->enderecos()
            ->oldest()
            ->value('end_id');
    }

    /**
     * Carrega relacionamentos em uma instância de unidade
     * 
     * @param Unidade $unidade Unidade para carregar relacionamentos
     * @param array $relations Relacionamentos para carregar
     * @return Unidade Unidade com relacionamentos carregados
     */
    public function loadRelations(Unidade $unidade, array $relations): Unidade
    {
        return $unidade->load($relations);
    }

    public function unidadeExists(int $unidId)
    {
        return $this->model->where('unid_id', $unidId)->exists();
    }
}