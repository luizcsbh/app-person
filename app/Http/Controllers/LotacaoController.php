<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Lotacao\StoreLotacaoRequest;
use App\Http\Requests\Lotacao\UpdateLotacaoRequest;
use App\Http\Resources\LotacaoResource;
use App\Http\Resources\ServidorUnidadeResource;
use App\Services\LotacaoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class LotacaoController extends Controller
{

    public function __construct(private LotacaoService $lotacaoService)
    {}

    /**
     * @OA\Get(
     *     path="/lotacoes",
     *     summary="Lista todos as lotações",
     *     description="Retorna uma lista de lotações armazenados no banco de dados.",
     *     tags={"Lotação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de lotações retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Lotacao"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhum lotação encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Não há lotações cadastrados!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar as lotações",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar as lotações."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $lotacoes = $this->lotacaoService->paginate($perPage);
    
            if ($lotacoes->total() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum lotação encontrada!',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }
    
            return LotacaoResource::collection($lotacoes)
                ->additional([
                    'success' => true,
                    'message' => 'Lista de lotações recuperada com sucesso'
                ],Response::HTTP_OK);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar lotações',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        /**
     * @OA\Get(
     *     path="/lotacoes/{id}",
     *     summary="Obtém os detalhes de uma lotação",
     *     description="Retorna os detalhes de uma lotação em específico pelo ID.",
     *     tags={"Lotação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da lotação",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da lotação retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Lotacao")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lotação não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="lotação não encontrada!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar as lotações",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar as lotações."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        
        try {

            $lotação = $this->lotacaoService->getLotacaoById($id);
            return new LotacaoResource($lotação);

        } catch (ResourceNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        
        } catch (\Exception $e) {
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/lotacoes",
     *     summary="Cria um lotação",
     *     description="Registra um lotação no banco de dados.",
     *     tags={"Lotação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados necessários para criar um lotação",
     *         @OA\JsonContent(
     *             required={"pes_id", "unid_id","lot_data_lotacao","lot_data_remocao","lot_portaria"},
     *             @OA\Property(property="pes_id", type="integer", example="1"),
     *             @OA\Property(property="unid_id", type="integer", example="1"),
     *             @OA\Property(property="lot_data_lotacao", type="date", format="date", example="2022-08-23"),
     *             @OA\Property(property="lot_data_remocao", type="date", format="date", example="2025-02-12"),
     *             @OA\Property(property="lot_portaria", type="string", example="Portaria 022/2025")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lotação criada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lotação criado com sucesso."),
     *             @OA\Property(property="data", ref="#/components/schemas/Lotacao")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao criar a lotação.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Erro ao criar o lotação.")
     *         )
     *     )
     * )
     */
    public function store(StoreLotacaoRequest $request)
    {
        try {
            $lotacao = $this->lotacaoService->createLotacao($request->validated());

            return response()->json([
                'success'=> true,
                'data' => new LotacaoResource($lotacao)
            ], Response::HTTP_CREATED);

        } catch (\DomainException $e) {
            return response()->json([
                'success'=> false,
                'message'=>$e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        
        } catch (\Exception $e) {
            return response()->json([
                'success'=> false,
                'message'=>  'Erro na lotação: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }   
    }

    /**
     * @OA\Put(
     *     path="/lotacoes/{id}",
     *     summary="Atualiza um lotação existente",
     *     description="Atualiza os dados de um lotação com base no ID fornecido.",
     *     tags={"Lotação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do lotação a ser atualizado",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true, 
     *         description="Dados para atualização do lotação",
     *         @OA\JsonContent(
     *             required={"lotação", "endereco"},
     *             @OA\Property(
     *                 property="lotacao",
     *                 type="object",
     *                 required={"pes_id", "unid_id","lot_data_lotacao","lot_data_remocao","lot_portaria"},
     *                 @OA\Property(property="pes_id", type="integer", example="1"),
     *                 @OA\Property(property="unid_id", type="integer", example="1"),
     *                 @OA\Property(property="lot_data_lotacao", type="integer", example="2022-08-23"),
     *                 @OA\Property(property="lot_data_remocao", type="string", example="2025-02-12"),
     *                 @OA\Property(property="lot_portaria", type="string", example="Portaria 023/2025")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lotação atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lotação atualizado com sucesso!"),
     *             @OA\Property(property="data", ref="#/components/schemas/Lotacao")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Registro não encontrado!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Registro não encontrado!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao processar a atualização do lotação",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Erro ao processar a solicitação.")
     *         )
     *     )
     * )
     */
    public function update(UpdateLotacaoRequest $request, $id)
    {
        try {
            
            $lotacao = $this->lotacaoService->updateLotacao($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'data' => new LotacaoResource($lotacao),
                'message' => 'Dados atualizados com sucesso'
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registro não encontrado'
            ], Response::HTTP_NOT_FOUND);
            
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de atualização do lotação: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/lotacoes/{id}",
     *     summary="Exclui um lotação",
     *     description="Exclui um lotação do banco de dados com base no ID fornecido.",
     *     tags={"Lotação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da lotação a ser excluído",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lotação excluída com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lotação excluída com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lotação não encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lotação não encontrada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao excluir o lotação devido a dependências",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao deletar o lotação. Possivelmente há dependências associadas.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao excluir o lotação",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao excluir o lotação."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro.")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->lotacaoService->deleteLotacao($id);

            return response()->json([
                'success' => true,
                'message' => 'Lotação excluída com sucesso.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return ApiResponse::handleException($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/lotacoes/unidade/{unid_id}/servidores",
     *     summary="Lista servidores lotados em uma unidade",
     *     description="Retorna uma lista paginada de servidores lotados em uma unidade específica",
     *     tags={"Lotação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="unid_id",
     *         in="path",
     *         required=true,
     *         description="ID da unidade",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Quantidade de itens por página",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Número da página",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de servidores retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="nome", type="string", example="João Silva"),
     *                 @OA\Property(property="idade", type="integer", example=35),
     *                 @OA\Property(property="unidade_lotacao", type="string", example="Secretaria de Educação")
     *             )),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unidade não encontrada ou sem servidores lotados",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unidade não encontrada ou sem servidores lotados")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar servidores",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar servidores"),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function servidoresPorUnidade(Request $request, $unidId)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $servidores = $this->lotacaoService->getServidoresPorUnidade($unidId, $perPage);

            if ($servidores->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum servidor encontrado nesta unidade',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }

            return ServidorUnidadeResource::collection($servidores)
                ->additional([
                    'success' => true,
                    'meta' => [
                        'total' => $servidores->total(),
                        'per_page' => $servidores->perPage(),
                        'current_page' => $servidores->currentPage(),
                        'last_page' => $servidores->lastPage(),
                        'from' => $servidores->firstItem(),
                        'to' => $servidores->lastItem()
                    ]
                ]);

        } catch (ResourceNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar servidores',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}

