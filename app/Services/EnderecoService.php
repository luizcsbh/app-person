<?php

namespace App\Services;

use Exception;
use App\Models\Pessoa;
use App\Models\Endereco;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\EnderecoRepositoryInterface;

/**
 * Serviço para gestão de operações relacionadas a endereços
 */
class EnderecoService
{
    /**
     * @param EnderecoRepositoryInterface $enderecoRepository Repositório de endereços
     */
    public function __construct(private EnderecoRepositoryInterface $enderecoRepository)
    {}

    /**
     * Retorna endereços paginados com relações
     * 
     * @param int $perPage Quantidade de registros por página
     * @return LengthAwarePaginator Lista paginada de endereços
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->enderecoRepository->paginateWithRelations(
            $perPage,
            ['cidade', 'pessoas']
        );
    }

    /**
     * Encontra um endereço pelo ID com relações
     * 
     * @param int $id ID do endereço
     * @return Endereco
     * @throws ModelNotFoundException Se o endereço não for encontrado
     */
    public function findById(int $id): Endereco
    {
        $endereco = $this->enderecoRepository->findByIdWithRelations(
            $id,
            ['cidade', 'pessoas']
        );

        if (!$endereco) {
            throw new ModelNotFoundException('Endereço não encontrado');
        }

        return $endereco;
    }

    /**
     * Cria um novo endereço
     * 
     * @param array $data Dados do endereço
     * @return Endereco Endereço criado
     * @throws Exception Se falhar na criação
     */
    public function createEndereco(array $data): Endereco
    {
        return DB::transaction(function () use ($data) {
            $endereco = $this->enderecoRepository->create(
                new Endereco($this->mapEnderecoData($data))
            );

            if (!$endereco->exists) {
                throw new Exception('Falha ao criar endereço');
            }

            return $endereco;
        });
    }

    /**
     * Atualiza um endereço existente
     * 
     * @param Endereco $endereco Endereço a ser atualizado
     * @param array $data Novos dados
     * @return Endereco Endereço atualizado
     * @throws Exception Se falhar na atualização
     */
    public function updateEndereco(Endereco $endereco, array $dadosEndereco): Endereco
    {
        return DB::transaction(function () use ($endereco, $dadosEndereco) {
            $dadosFiltrados = array_filter($dadosEndereco, function ($valor) {
                return !is_null($valor);
            });

            if (!$this->enderecoRepository->update($endereco, $dadosFiltrados)) {
                throw new Exception('Falha ao atualizar endereço');
            }
            return $endereco->refresh();
        });
    }

    /**
     * Exclui um endereço após verificar dependências
     * 
     * @param Endereco $endereco Endereço a ser excluído
     * @throws Exception Se existirem dependências ou falha na exclusão
     */
    public function deleteEndereco(Endereco $endereco): void
    {
        DB::transaction(function () use ($endereco) {
            $this->checkDependencies($endereco);
            
            if (!$this->enderecoRepository->delete($endereco)) {
                throw new Exception('Falha ao excluir endereço');
            }
        });
    }

    /**
     * Exclui todos endereços de uma pessoa
     * 
     * @param Pessoa $pessoa Pessoa para remover endereços
     */
    public function deleteAllFromPessoa(Pessoa $pessoa): void
    {
        $pessoa->enderecos->each(function ($endereco) {
            $this->deleteEndereco($endereco);
        });
    }

    /**
     * Verifica dependências que impedem a exclusão
     * 
     * @param Endereco $endereco Endereço para verificação
     * @throws Exception Se existirem relacionamentos ativos
     */
    public function checkDependencies(Endereco $endereco): void
    {
        $dependencies = [
            'pessoas' => 'pessoas vinculadas'
            
        ];

        foreach ($dependencies as $relation => $message) {
            if ($endereco->$relation()->exists()) {
                throw new Exception("Não é possível excluir. Existem $message.");
            }
        }
    }

    /**
     * Filtra e mapeia os dados válidos para endereço
     * 
     * @param array $data Dados brutos
     * @return array Dados filtrados
     */
    private function mapEnderecoData(array $data): array
    {
        $allowedKeys = [
            'cid_id',
            'end_tipo_logradouro',
            'end_logradouro',
            'end_numero',
            'end_complemento',
            'end_bairro'
        ];

        $mappedData = [];
        foreach ($allowedKeys as $key) {
            if (array_key_exists($key, $data)) {
                $mappedData[$key] = $data[$key];
            }
        }

        return $mappedData;
    }
}