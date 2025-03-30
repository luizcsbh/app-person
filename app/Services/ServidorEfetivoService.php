<?php

namespace App\Services;

use Exception;
use App\Models\Pessoa;
use App\Models\ServidorEfetivo;
use App\Repositories\EnderecoRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\PessoaRepository;
use App\Repositories\ServidorEfetivoRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ServidorEfetivoService
{
    public function __construct(
        private PessoaRepository $pessoaRepository,
        private PessoaService $pessoaService,
        private EnderecoService $enderecoService,
        private EnderecoRepository $enderecoRepository,
        private ServidorEfetivoRepository $servidorEfetivoRepository
    ) {}

    /**
     * Retorna todas as relações Pessoa cadastradas com paginacao.
     *
     * @return mixed Lista de relações Pessoa paginada.
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->servidorEfetivoRepository->paginateWithRelations(
            $perPage,
            ['pessoa', 'pessoa.enderecos']
        );
    }

    /**
     * Encontra servidor efetivo pelo ID
     * 
     * @param int $id ID do servidor efetivo
     * @return ServidorEfetivo
     * @throws ModelNotFoundException
     */
    public function getById(int $id): ServidorEfetivo
    {
        $servidor = $this->servidorEfetivoRepository->findByIdWithRelations(
            $id,
            ['pessoa', 'pessoa.enderecos', 'pessoa.servidorTemporario']
        );

        if (!$servidor) {
            throw new ModelNotFoundException('Servidor Efetivo não encontrado');
        }

        return $servidor;
    }

    /**
     * Encontra servidor efetivo com dados da pessoa
     * 
     * @param int $id ID do servidor efetivo
     * @return ServidorEfetivo
     * @throws ResourceNotFoundException
     */
    public function findWithPessoa(int $id): ServidorEfetivo
    {
        $servidor = $this->servidorEfetivoRepository->findByIdWithRelations(
            $id,
            ['pessoa', 'pessoa.enderecos']
        );

        if (!$servidor) {
            throw new ResourceNotFoundException('Servidor efetivo não encontrado');
        }

        return $servidor;
    }
    
    /**
     * Cria novo servidor efetivo com dados relacionados
     * 
     * @param array $validatedData Dados validados
     * @return array
     * @throws \RuntimeException|\DomainException
     */
    public function createServidorEfetivo(array $validatedData): ServidorEfetivo
    {  
        return DB::transaction(function () use ($validatedData) {
            $pessoa = $this->pessoaService->createPessoa($validatedData);
            $endereco = $this->enderecoService->createEndereco($validatedData);

            $this->pessoaRepository->attachEndereco($pessoa, $endereco->end_id);

            return $this->createServidorEfetivoRecord($pessoa, $validatedData['se_matricula']);
        });
    }

      /**
     * Cria registro de servidor efetivo
     */
    private function createServidorEfetivoRecord(Pessoa $pessoa, string $matricula): ServidorEfetivo
    {
        return $this->servidorEfetivoRepository->create(
            new ServidorEfetivo([
                'pes_id' => $pessoa->pes_id,
                'se_matricula' => $matricula
            ])
        );
    }

        /**
     * Atualiza dados do servidor efetivo e relacionados
     * 
     * @param int $pesId ID da pessoa
     * @param array $data Dados para atualização
     * @return ServidorEfetivo
     * @throws Exception
     */
    public function updateServidorEfetivo(int $pesId, array $data): ServidorEfetivo
    {
        $pessoa = Pessoa::with(['servidorEfetivo', 'enderecos'])
            ->findOrFail($pesId);
    
        return DB::transaction(function () use ($pessoa, $data) {
            try {
               
                $this->updatePessoa($pessoa, $data);
                
                $this->updateEnderecoPrincipal($pessoa, $data);
               
                $this->updateServidorEfetivoData($pessoa->servidorEfetivo, $data);
    
                return $pessoa->servidorEfetivo->refresh()->load([
                    'pessoa.enderecos'
                ]);
    
            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Rollback realizado: ' . $e->getMessage());
            }
        });
    }

    private function updatePessoa(Pessoa $pessoa, array $data): void
    {
        $dadosPessoa = array_filter([
            'pes_nome' => $data['pes_nome'] ?? null,
            'pes_cpf' => $data['pes_cpf'] ?? null,
            'pes_data_nascimento' => $data['pes_data_nascimento'] ?? null,
            'pes_sexo' => $data['pes_sexo'] ?? null,
            'pes_mae' => $data['pes_mae'] ?? null,
            'pes_pai' => $data['pes_pai'] ?? null
        ]);

        if (!empty($dadosPessoa) && !$this->pessoaRepository->update($pessoa, $dadosPessoa)) {
            throw new \Exception('Falha ao atualizar dados da pessoa');
        }
    }

    private function updateEnderecoPrincipal(Pessoa $pessoa, array $data): void
    {
        if ($pessoa->enderecos->isNotEmpty()) {
            $endereco = $pessoa->enderecos->first();
            $dadosEndereco = array_filter([
                'end_tipo_logradouro' => $data['end_tipo_logradouro'] ?? null,
                'end_logradouro' => $data['end_logradouro'] ?? null,
                'end_numero' => $data['end_numero'] ?? null,
                'end_complemento' => $data['end_complemento'] ?? null,
                'end_bairro' => $data['end_bairro'] ?? null,
                'cid_id' => $data['cid_id'] ?? null
            ]);

            if (!empty($dadosEndereco) && !$this->enderecoRepository->update($endereco, $dadosEndereco)) {
                throw new \Exception('Falha ao atualizar endereço');
            }
        }
    }

    private function updateServidorEfetivoData(ServidorEfetivo $servidor, array $data): void
    {
        $dadosServidor = array_filter([
            'se_matricula' => $data['se_matricula'] ?? null
        ]);

        if (!empty($dadosServidor) && !$this->servidorEfetivoRepository->update($servidor, $dadosServidor)) {
            throw new \Exception('Falha ao atualizar dados do servidor');
        }
    }

     /**
     * Exclui servidor efetivo pelo ID
     * 
     * @param int $id ID do servidor efetivo
     * @throws ModelNotFoundException|Exception
     */
    public function deleteServidorEfetivo($pesId): void
    {
        $pessoa = $this->pessoaService->getPessoaById($pesId);

       

        $servidorEfetivo = $pessoa->servidorEfetivo;

        DB::transaction(function () use ($pessoa, $servidorEfetivo) {
            
            //$this->checkDependencies($servidorEfetivo);
            $this->checkAndHandleDependencies($servidorEfetivo, true, true);
            //$this->enderecoService->deleteAllFromPessoa($pessoa);
            $this->servidorEfetivoRepository->delete($servidorEfetivo);
            $this->pessoaService->deletePessoa($pessoa);
        });
    }

    /**
     * Verifica e gerencia dependências de um servidor efetivo de forma atômica.
     * Pode dissociar ou deletar registros associados conforme configuração.
     *
     * @param ServidorEfetivo $servidor Servidor a verificar
     * @param bool $forceDelete Quando true, remove automaticamente as associações permitidas
     * @param bool $cascadeDelete Quando true, deleta os registros associados (ao invés de apenas dissociar)
     * @return void
     * @throws \RuntimeException Se existirem dependências não removíveis ou falha na operação
     */
    public function checkAndHandleDependencies(
        ServidorEfetivo $servidor,
        bool $forceDelete = false,
        bool $cascadeDelete = false
    ): void {
        DB::beginTransaction();

        try {
            $relationships = [
                'pessoa' => [
                    'message' => 'pessoa associada',
                    'canDissociate' => false,
                    'canDelete' => true,
                    'handler' => fn($relation) => $cascadeDelete ? $relation->delete() : $relation->dissociate()
                ]
            ];

            foreach ($relationships as $relationship => $config) {
                $relation = $servidor->{$relationship}();
                
                if ($relation->exists()) {
                    if ($forceDelete && ($cascadeDelete ? $config['canDelete'] : $config['canDissociate'])) {
                        // Executa a ação configurada (delete ou dissociate)
                        if (isset($config['handler'])) {
                            $config['handler']($relation);
                            $servidor->save();
                        }
                    } else {
                        DB::rollBack();
                        $action = $cascadeDelete ? 'deletar' : 'dissociar';
                        throw new \RuntimeException(
                            "Não é possível excluir o servidor. Existe {$config['message']}. " .
                            ($config['canDissociate'] || $config['canDelete'] 
                                ? "Use forceDelete=true para {$action} automaticamente." 
                                : 'Esta associação não pode ser removida automaticamente.')
                        );
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \RuntimeException("Falha ao processar dependências: " . $e->getMessage());
        }
    }

}