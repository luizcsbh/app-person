<?php

namespace App\Services;

use App\Exceptions\LotacaoAtivaException;
use Exception;
use App\Models\Lotacao;
use App\Repositories\Contracts\LotacaoRepositoryInterface;
use App\Repositories\LotacaoRepository;
use App\Repositories\PessoaRepository;
use App\Repositories\UnidadeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Serviço para gerenciamento de Lotações.
 */
class LotacaoService
{
    public function __construct( 
        private LotacaoRepository $lotacaoRepository,
        private PessoaRepository $pessoaRepository,
        private UnidadeRepository $unidadeRepository
        
    )
    {}

    /**
     * Obtém uma lotacao pelo ID.
     *
     * @param int $id
     * @return Lotacao
     * @throws ModelNotFoundException Se a lotacao não for encontrada.
     */
    public function getLotacaoById($id): Lotacao
    {
        
        $lotacao = $this->lotacaoRepository->findByIdWithRelations($id);

        if (!$lotacao) {
            throw new ModelNotFoundException('Lotacao não encontrado.');
        }
        return $lotacao;
    }

    /**
     * Retorna lotações paginadas com os endereços associados.
     *
     * @param int $perPage Número de itens por página.
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->lotacaoRepository->paginateWithRelations(
            $perPage,
            ['pessoa','unidade']
        );
    }
    
    /**
     * Cria uma nova lotação e associa um endereço.
     *
     * @param array $validatedData Dados validados.
     * @return Lotacao Lotacao criada.
     */
    public function createLotacao(array $data): Lotacao
    {
        DB::beginTransaction();

        try {

            $lotacaoAtiva = $this->lotacaoRepository->findActiveCapacity($data['pes_id'], $data['unid_id']);
    
            if ($lotacaoAtiva) {
                throw new LotacaoAtivaException();
            }
    
            $lotacao = $this->lotacaoRepository->create($data);
            DB::commit();
            return $lotacao;
    
        } catch (LotacaoAtivaException $e) {
            DB::rollBack();
            throw $e; 
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao criar lotação: " . $e->getMessage());
        }
    }

    /**
     * Atualiza uma lotacao e seu endereço.
     *
     * @param int $id ID da lotacao.
     * @param array $data Dados a serem atualizados.
     * @return Lotacao Lotacao atualizada.
     * @throws Exception Se ocorrer um erro na atualização.
     */
    public function updateLotacao(int $lotId, array $data)
    {
        DB::beginTransaction();

        try {
            
            $lotacao = $this->getLotacaoById($lotId);
            $this->lotacaoRepository->update($lotacao, $data);
            DB::commit();
            return $lotacao; 
    
        } catch (Exception $e) {
            DB::rollBack(); 
            throw new Exception("Falha ao atualizar lotação: " . $e->getMessage());
        }
    }

    /**
     * Deleta uma lotacao e seus endereços associados.
     *
     * @param int $unidId ID da lotacao.
     */
    public function deleteLotacao($id): void
    {
        DB::beginTransaction();

        try {
        
            $lotacao = $this->getLotacaoById($id);
            $lotacao->delete();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Erro ao excluir a lotação: ' . $e->getMessage());
        }
    }

   /**
     * Obtém lista paginada de servidores alocados em uma unidade específica
     *
     * @param int $unidId ID da unidade para filtrar
     * @param int $perPage Número de itens por página (padrão: 10)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception Quando a unidade especificada não é encontrada (HTTP 404)
     *
     * @example
     * // Uso básico
     * $servidores = $service->getServidoresPorUnidade(1);
     *
     * // Com paginação personalizada
     * $servidores = $service->getServidoresPorUnidade(1, 20);
     */
    public function getServidoresPorUnidade(int $unidId, int $perPage)
    {
        if (!$this->unidadeRepository->unidadeExists($unidId)) {
            throw new \Exception('Unidade não encontrada!', 404); 
        }

        $query = $this->pessoaRepository->buscarServidoresPorUnidade($unidId, $perPage);
        
        return $query;
            
    }
    
}