<?php

namespace App\Services;

use App\Models\Endereco;
use Exception;
use App\Models\Pessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Contracts\PessoaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PessoaService
{
    public function __construct( private PessoaRepositoryInterface $pessoaRepository)
    {}

    public function getAllPessoas(): iterable
    {
        return $this->pessoaRepository->allWithRelations(['enderecos', 'servidorEfetivo', 'servidorTemporario']);
    }

    public function getPessoaById($id): Pessoa
    {
        $pessoa = $this->pessoaRepository->findByIdWithRelations(
            $id,
            ['enderecos', 'servidorEfetivo', 'servidorTemporario']
        );

        if (!$pessoa) {
            throw new ModelNotFoundException('Pessoa não encontrado.');
        }
        return $pessoa;
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->pessoaRepository->paginateWithRelations(
            $perPage,
            ['enderecos','servidorEfetivo']
        );
    }
    
    /**
     * Cria uma nova pessoa com os dados fornecidos em uma transação atômica.
     * 
     * @param array<string, mixed> $data Dados da pessoa a ser criada
     * @return Pessoa A pessoa criada e persistida
     * @throws \InvalidArgumentException Se os dados estiverem incompletos ou inválidos
     * @throws \RuntimeException Se a persistência falhar
     * @throws \Exception Para erros inesperados durante o processo
     */
    public function createPessoa(array $data): Pessoa
    {
        DB::beginTransaction();

        try {
            
            $pessoaData = $this->mapPessoaData($data);
            
            if (empty($pessoaData)) {
                throw new \InvalidArgumentException('Dados da pessoa inválidos ou incompletos');
            }

            $pessoa = new Pessoa($pessoaData);
            
            $createdPessoa = $this->pessoaRepository->create($pessoa);

            if (!$createdPessoa->exists) {
                throw new \RuntimeException('Falha na persistência da pessoa no banco de dados');
            }

            DB::commit();
            return $createdPessoa;

        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\RuntimeException $e) {
            DB::rollBack();
            throw new \RuntimeException('Erro ao criar pessoa: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Erro inesperado ao criar pessoa: ' . $e->getMessage(), 0, $e);
        }
    }

    public function updatePessoa(Pessoa $pessoa, array $data): Pessoa
    {
        
        return DB::transaction(function () use ($pessoa, $data) {
            if (!$this->pessoaRepository->update($pessoa, $this->mapPessoaData($data['pessoa']))) {
                throw new Exception('Falha na atualização da pessoa');
            }

            return $pessoa->refresh()->loadRelations();
        });
    }

    public function getMainEnderecoId(Pessoa $pessoa): ?Endereco
    {
        return $pessoa->enderecos()->oldest()->first();
    }

    public function deletePessoa(Pessoa $pessoa): void
    {
        DB::transaction(function () use ($pessoa) {
            $this->checkDependencies($pessoa);
            
            if (!$this->pessoaRepository->delete($pessoa)) {
                throw new Exception('Falha na exclusão da pessoa');
            }
        });
    }

    

      /**
     * Verifica se existem dependências associadas a uma pessoa como fotos, lotações, servidores temporarios,
     *                     servidores efetivos e endereços.
     *
     * @param \App\Models\Pessoa $pessoa A unidade que será verificada quanto a dependências.
     * 
     * @throws \Exception Se a pessoa tiver fotos, lotações, sevidores temporarios, efetivos ou endereços
     *                     associadas, uma exceção será lançada com uma mensagem informando qual tipo de 
     *                     dependência está impedindo a exclusão.
     * 
     * @return void
     */
    public function checkDependencies(Pessoa $pessoa): void
    {
        $dependencies = [
            'foto' => 'foto associadas',
            'lotacao' => 'lotações associadas',
            'ServidorTemporario' => 'servidores temporários associados',
            'servidorEfetivo' => 'servidor efetivo associado',
            'enderecos' => 'endereços associados'
        ];
    
        foreach ($dependencies as $relationship => $message) {
            if ($pessoa->{$relationship}()->exists()) {
                throw new Exception("Não é possível excluir a pessoa. Existem {$message} a ela.");
            }
        }
    }
    private function mapPessoaData(array $data): array
    {
        $allowedKeys = [
            'pes_nome',
            'pes_data_nascimento',
            'pes_cpf',
            'pes_sexo',
            'pes_mae',
            'pes_pai'
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