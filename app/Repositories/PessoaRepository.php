<?php

namespace App\Repositories;

use App\Models\Pessoa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\PessoaRepositoryInterface;
use Illuminate\Support\Facades\Log;

/**
 * Repositório para operações de banco de dados relacionadas a pessoas
 */
class PessoaRepository implements PessoaRepositoryInterface
{
    /**
     * @param Pessoa $model Instância do modelo Pessoa
     */
    public function __construct(
        private Pessoa $model
    ) {}

    /**
     * Retorna todas as pessoas com relacionamentos
     * 
     * @param array $relations Relacionamentos para carregar
     * @return Collection Coleção de pessoas com relacionamentos
     */
    public function allWithRelations(array $relations = []): Collection
    {
        return $this->model
            ->with($relations)
            ->orderBy('pes_id')
            ->get();
    }

    /**
     * Retorna pessoas paginadas com relacionamentos
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
     * Encontra uma pessoa por ID com relacionamentos
     * 
     * @param int $id ID da pessoa
     * @param array $relations Relacionamentos para carregar
     * @return Pessoa|null Pessoa encontrada ou null
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Pessoa
    {
        return $this->model
            ->with($relations)
            ->find($id);
    }

    /**
     * Cria uma nova pessoa no banco de dados
     * 
     * @param Pessoa $pessoa Instância da pessoa a ser criada
     * @return Pessoa Pessoa criada com dados atualizados
     * @throws \RuntimeException Se falhar na criação
     */
    public function create(Pessoa $pessoa): Pessoa
    {
        if (!$pessoa->save()) {
            throw new \RuntimeException('Falha ao criar registro de pessoa');
        }
        return $pessoa->refresh();
    }

    /**
     * Atualiza os dados de uma pessoa
     * 
     * @param Pessoa $pessoa Instância da pessoa a ser atualizada
     * @param array $data Dados para atualização
     * @return bool True se a atualização foi bem sucedida
     */
    public function update(Pessoa $pessoa, array $data): bool
    {
        try {
            return $pessoa->update($data);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar pessoa: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui uma pessoa do banco de dados
     * 
     * @param Pessoa $pessoa Instância da pessoa a ser excluída
     * @return bool True se a exclusão foi bem sucedida
     * @throws \RuntimeException Se ocorrer erro na exclusão
     */
    public function delete(Pessoa $pessoa): bool
    {
        try {
            return $pessoa->delete();
        } catch (\Exception $e) {
            Log::error('Falha ao excluir pessoa: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vincula um endereço a uma pessoa
     * 
     * @param Pessoa $pessoa Pessoa para vincular o endereço
     * @param int $enderecoId ID do endereço
     * @throws \RuntimeException Se falhar no vínculo
     */
    public function attachEndereco(Pessoa $pessoa, int $enderecoId): void
    {
        try {
            $pessoa->enderecos()->attach($enderecoId, [
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException('Falha ao vincular endereço: ' . $e->getMessage());
        }
    }

    /**
     * Obtém o ID do endereço principal de uma pessoa
     * 
     * @param Pessoa $pessoa Pessoa para consulta
     * @return int|null ID do endereço mais antigo ou null
     */
    public function getMainEndereco(Pessoa $pessoa): ?int
    {
        return $pessoa->enderecos()
            ->oldest()
            ->value('end_id');
    }

    /**
     * Carrega relacionamentos em uma instância de pessoa
     * 
     * @param Pessoa $pessoa Pessoa para carregar relacionamentos
     * @param array $relations Relacionamentos para carregar
     * @return Pessoa Pessoa com relacionamentos carregados
     */
    public function loadRelations(Pessoa $pessoa, array $relations): Pessoa
    {
        return $pessoa->load($relations);
    }

    public function calcularIdade($dataNascimento)
    {
        $nascimento = new \DateTime($dataNascimento);
        $hoje = new \DateTime();
        return $hoje->diff($nascimento)->y;
    }
}