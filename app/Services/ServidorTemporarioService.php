<?php

namespace App\Services;

use Exception;
use App\Models\Pessoa;
use Illuminate\Http\Response;
use App\Models\ServidorTemporario;
use App\Repositories\EnderecoRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\PessoaRepository;
use App\Repositories\ServidorTemporarioRepository;

use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ServidorTemporarioService
{
    /**
     * @param PessoaRepository $pessoaRepository
     * @param PessoaService $pessoaService
     * @param EnderecoService $enderecoService
     * @param ServidorTemporarioRepository $servidorTemporarioRepository
     */
    public function __construct(
        private PessoaRepository $pessoaRepository,
        private PessoaService $pessoaService,
        private EnderecoService $enderecoService,
        private EnderecoRepository $enderecoRepository,
        private ServidorTemporarioRepository $servidorTemporarioRepository
    ) {}

    /**
     * Retorna todos os servidores temporarios paginados com suas relações
     * 
     * @param int $perPage Quantidade de registros por página
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->servidorTemporarioRepository->paginateWithRelations(
            $perPage,
            ['pessoa', 'pessoa.enderecos']
        );
    }

    /**
     * Encontra um servidor temporario pelo ID com relações
     * 
     * @param int $id ID do servidor temporario
     * @return ServidorTemporario
     * @throws ModelNotFoundException
     */
    public function getById(int $id): ServidorTemporario
    {
        $servidor = $this->servidorTemporarioRepository->findByIdWithRelations(
            $id,
            ['pessoa', 'pessoa.enderecos', 'pessoa.servidorTemporario']
        );

        if (!$servidor) {
            throw new ModelNotFoundException('Servidor Temporario não encontrado');
        }

        return $servidor;
    }

    /**
     * Encontra um servidor temporario com dados da pessoa
     * 
     * @param int $id ID do servidor temporario
     * @return ServidorTemporario
     * @throws ResourceNotFoundException
     */
    public function findWithPessoa(int $id): ServidorTemporario
    {
        $servidor = $this->servidorTemporarioRepository->findByIdWithRelations(
            $id,
            ['pessoa', 'pessoa.enderecos']
        );

        if (!$servidor) {
            throw new ResourceNotFoundException('Servidor temporario não encontrado');
        }

        return $servidor;
    }
    
    /**
     * Cria um novo servidor temporario com dados relacionados
     * 
     * @param array $validatedData Dados validados
     * @return ServidorTemporario
     * @throws \Throwable
     */
    public function createServidorTemporario(array $validatedData): ServidorTemporario
    {  
        return DB::transaction(function () use ($validatedData) {
            try {
                $dataAdmissao = new \DateTimeImmutable($validatedData['st_data_admissao']);

            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Formato de data inválido. Use o formato AAAA-MMM_DD');
            }

            $pessoa = $this->pessoaService->createPessoa($validatedData);
            $endereco = $this->enderecoService->createEndereco($validatedData);

            $this->pessoaRepository->attachEndereco($pessoa, $endereco->end_id);
            return $this->createServidorTemporarioRecord(
                $pessoa,
                $dataAdmissao
            );
        });
    }

    /**
     * Cria registro de servidor temporário na base de dados
     * 
     * @param Pessoa $pessoa Pessoa associada ao servidor
     * @param \DateTimeInterface $dataAdmissao Data de admissão
     * @return ServidorTemporario
     * @throws \InvalidArgumentException Se a data for inválida
     */
    private function createServidorTemporarioRecord(
        Pessoa $pessoa,
        \DateTimeInterface $dataAdmissao
    ): ServidorTemporario {
        try {
            return $this->servidorTemporarioRepository->create(
                new ServidorTemporario([
                    'pes_id' => $pessoa->pes_id,
                    'st_data_admissao' => $dataAdmissao->format('Y-m-d')
                ])
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Falha ao criar registro: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza dados do servidor temporario e relações
     * 
     * @param int $pesId ID da pessoa
     * @param array $data Dados para atualização
     * @return ServidorTemporario
     * @throws \Throwable
     */
    public function updateServidorTemporario(int $pesId, array $data): ServidorTemporario
    {
       
        $pessoa = Pessoa::with(['servidorTemporario', 'enderecos'])
            ->findOrFail($pesId);
    
        return DB::transaction(function () use ($pessoa, $data) {
            try {
                
                $this->updatePessoa($pessoa, $data);
                
                $this->updateEndereco($pessoa, $data);
                
                $this->updateServidor($pessoa->servidorTemporario, $data);
            
                return $pessoa->servidorTemporario->refresh()
                    ->load(['pessoa.enderecos']);
            
            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Falha na trasanção :' . $e->getMessage());
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

    private function updateEndereco(Pessoa $pessoa, array $data): void
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

    private function updateServidor(ServidorTemporario $servidor, array $data): void
    {
        $dadosServidor = array_filter([
            'st_data_admissao' => $data['st_data_admissao'] ?? null,
            'st_data_demissao' => $data['st_data_demissao'] ?? null
        ]);

        if (!empty($dadosServidor) && !$this->servidorTemporarioRepository->update($servidor, $dadosServidor)) {
            throw new \Exception('Falha ao atualizar dados do servidor');
        }
    }

    /**
     * Exclui um servidor temporario e suas relações
     * 
     * @param int $pesId ID da pessoa
     * @throws \Throwable
     */
    public function deleteServidorTemporario($pesId): void
    {
        $pessoa = $this->pessoaService->getPessoaById($pesId);

        $servidorTemporario = $pessoa->servidorTemporario;

        DB::transaction(function () use ($pessoa, $servidorTemporario) {
            
            $this->pessoaService->deletePessoa($pessoa);

            $this->enderecoService->deleteAllFromPessoa($pessoa);
            $this->servidorTemporarioRepository->delete($servidorTemporario);
            
        });
    }


}