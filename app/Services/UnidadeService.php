<?php

namespace App\Services;

use App\Models\Endereco;
use Exception;
use App\Models\Unidade;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Contracts\UnidadeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UnidadeService
{
    public function __construct( 
        private UnidadeRepositoryInterface $unidadeRepository,
        private EnderecoService $enderecoService,
    )
    {}

    public function getAllUnidades(): iterable
    {
        return $this->unidadeRepository->allWithRelations(['enderecos']);
    }

    public function getUnidadeById($id): Unidade
    {
        
        $unidade = $this->unidadeRepository->findByIdWithRelations(
            $id,
            ['enderecos']
        );

        if (!$unidade) {
            throw new ModelNotFoundException('Unidade não encontrado.');
        }
        return $unidade;
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->unidadeRepository->paginateWithRelations(
            $perPage,
            ['enderecos']
        );
    }
    
    public function createUnidade(array $validatedData): Unidade
    {
        return DB::transaction(function () use ($validatedData) {
            $unidade = $this->unidadeRepository->create(new Unidade($this->mapUnidadeData($validatedData)));
            $endereco = $this->enderecoService->createEndereco($validatedData);

            $this->unidadeRepository->attachEndereco($unidade, $endereco->end_id);

            return $unidade->refresh();
        });
    }

    public function updateUnidade(int $endId, array $data): Unidade
    {
        
        $unidade = $this->getUnidadeById($endId);

        return DB::transaction(function () use ($unidade, $data) {
            try {
                $this->atualizarUnidade($unidade, $data);
                $this->atualizarEndereco($unidade, $data);

                return $unidade->refresh()->load(['enderecos']);

            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Rollback realizado: ' . $e->getMessage());
            }  
        });
    }

    private function atualizarUnidade(Unidade $unidade, array $data)
    {
        
        $dadosUnidade = array_filter([
            'unid_nome' => $data['unid_nome'] ?? null,
            'unid_sigla' => $data['unid_sigla'] ?? null,

        ]);

        if (!empty($dadosUnidade) && !$this->unidadeRepository->update($unidade, $dadosUnidade)) {
            throw new \Exception('Falha ao atulizar unidades!');
        }
    }

    private function atualizarEndereco(Unidade $unidade, array $data): void
    {
        if ($unidade->enderecos->isNotEmpty()) {
            $endereco = $unidade->enderecos->first();
            $dadosEndereco = array_filter([
                'end_tipo_logradouro' => $data['end_tipo_logradouro'] ?? null,
                'end_logradouro' => $data['end_logradouro'] ?? null,
                'end_numero' => $data['end_numero'] ?? null,
                'end_complemento' => $data['end_complemento'] ?? null,
                'end_bairro' => $data['end_bairro'] ?? null,
                'cid_id' => $data['cid_id'] ?? null
            ]);

            if (!empty($dadosEndereco) && !$this->enderecoService->updateEndereco($endereco, $dadosEndereco)) {
                throw new \Exception('Falha ao atualizar endereço');
            }
        }
    }

    public function deleteUnidade($unidId): void
    {

        $unidade = $this->getUnidadeById($unidId);
        
        DB::transaction(function () use ($unidade) {
            
            if (!$this->unidadeRepository->delete($unidade)) {
                throw new Exception('Falha na exclusão da unidade');
            }
        });
    }
    
    private function mapUnidadeData(array $data): array
    {
        return array_intersect_key($data, array_flip(['unid_nome', 'unid_sigla']));
    }
    
}