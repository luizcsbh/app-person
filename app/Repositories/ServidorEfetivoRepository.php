<?php

namespace App\Repositories;

use App\Models\ServidorEfetivo;
use App\Models\Pessoa;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\ServidorEfetivoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServidorEfetivoRepository implements ServidorEfetivoRepositoryInterface
{
    /**
     * @param ServidorEfetivo $model Instância do modelo ServidorEfetivo
     */
    public function __construct(private ServidorEfetivo $model)
    {
    }

    /**
     * Retorna todos os servidores efetivos com relacionamentos
     * 
     * @param array $relations Relacionamentos para carregar
     * @return Collection Coleção de servidores efetivos
     */
    public function allWithRelations(array $relations = []): Collection
    {
        return $this->model->with($relations)->orderBy('id')->get();
    }

    /**
     * Retorna servidores efetivos paginados com relacionamentos
     * 
     * @param int $perPage Número de itens por página
     * @param array $relations Relacionamentos para carregar
     * @return LengthAwarePaginator Resultado paginado
     */
    public function paginateWithRelations(int $perPage = 10, array $relations = []): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('pes_id', 'asc')
            ->with($relations)
            ->paginate($perPage);
    }

    /**
     * Encontra um servidor efetivo pelo ID com relacionamentos
     * 
     * @param int $id ID do servidor efetivo
     * @param array $relations Relacionamentos para carregar
     * @return ServidorEfetivo|null Servidor encontrado ou null
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?ServidorEfetivo
    {
        return $this->model
            ->with($relations)
            ->find($id);
    }

    /**
     * Cria um novo registro de servidor efetivo
     * 
     * @param ServidorEfetivo $servidor Instância do servidor a ser criado
     * @return ServidorEfetivo Servidor criado com dados atualizados
     * @throws \RuntimeException Se falhar na criação
     */
    public function create(ServidorEfetivo $servidor): ServidorEfetivo
    {
        if (!$servidor->save()) {
            throw new \RuntimeException('Falha ao criar servidor efetivo');
        }
        return $servidor->refresh();
    }

    /**
     * Atualiza os dados de um servidor efetivo
     * 
     * @param ServidorEfetivo $servidor Instância do servidor a ser atualizado
     * @param array $data Dados para atualização
     * @return bool True se a atualização foi bem sucedida
     */
    public function update(ServidorEfetivo $servidor, array $data): bool
    {
        return $servidor->update($data);
    }

    /**
     * Exclui um registro de servidor efetivo
     * 
     * @param ServidorEfetivo $servidor Instância do servidor a ser excluído
     * @return bool True se a exclusão foi bem sucedida
     * @throws \RuntimeException Se ocorrer erro na exclusão
     */
    public function delete(ServidorEfetivo $servidor): bool
    {
        try {
            return $servidor->delete();
        } catch (\Exception $e) {
            throw new \RuntimeException('Falha ao excluir servidor efetivo: ' . $e->getMessage());
        }
    }

    /**
     * Encontra um servidor efetivo pelo ID da pessoa relacionada
     * 
     * @param int $pesId ID da pessoa
     * @param array $relations Relacionamentos para carregar
     * @return ServidorEfetivo|null Servidor encontrado ou null
     */
    public function findByPessoaId(int $pesId, array $relations = []): ?ServidorEfetivo
    {
        return $this->model
            ->with($relations)
            ->where('pes_id', $pesId)
            ->first();
    }

    /**
     * Atualiza a matrícula de um servidor efetivo
     * 
     * @param ServidorEfetivo $servidor Instância do servidor
     * @param string $matricula Nova matrícula
     * @return bool True se a atualização foi bem sucedida
     */
    public function updateMatricula(ServidorEfetivo $servidor, string $matricula): bool
    {
        return $servidor->update(['se_matricula' => $matricula]);
    }

    /**
     * Carrega relacionamentos em uma instância de servidor efetivo
     * 
     * @param ServidorEfetivo $servidor Instância do servidor
     * @param array $relations Relacionamentos para carregar
     * @return ServidorEfetivo Servidor com relacionamentos carregados
     */
    public function loadRelations(ServidorEfetivo $servidor, array $relations): ServidorEfetivo
    {
        return $servidor->load($relations);
    }

    public function buscarEnderecoPorNomeServidor(string $parteNome): Collection
    {
        return Pessoa::where('pes_nome', 'like', "%{$parteNome}%")
        ->whereHas('servidores_efetivos', function ($query) {
            $query->whereHas('lotacoes', function ($query) {
                $query->whereHas('unidades', function ($query) {
                    $query->with(['enderecos']);
                });
            });
        })
        ->with([
            'servidores_efetivo.lotacoes.unidades.enderecos'
        ])
        ->get()
            ->map(function($servidor) {
                return [
                    'servidor' => $servidor->pes_nome,
                    'unidade' => $servidor->lotacao->unidade->unid_nome,
                    'endereco' => $servidor->lotacao->unidade->endereco
                ];
            });
    }
    
}