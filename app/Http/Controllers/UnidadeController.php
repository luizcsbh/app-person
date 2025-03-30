<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Unidade\StoreUnidadeRequest;
use App\Http\Requests\Unidade\UpdateUnidadeRequest;
use App\Http\Resources\UnidadeResource;
use App\Services\UnidadeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UnidadeController extends Controller
{

    public function __construct(private UnidadeService $unidadeService)
    {}

    /**
     * @OA\Get(
     *     path="/unidades",
     *     summary="Lista todos as unidades",
     *     description="Retorna uma lista de unidades armazenados no banco de dados.",
     *     tags={"Unidade"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de unidades retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Unidade"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhum unidade encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Não há unidades cadastrados!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar os unidades",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar os unidades."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $unidades = $this->unidadeService->paginate($perPage);
    
            if ($unidades->total() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum unidade encontrada',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }
    
            return UnidadeResource::collection($unidades)
                ->additional([
                    'success' => true,
                    'message' => 'Lista de unidades recuperada com sucesso'
                ],Response::HTTP_OK);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar unidades',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        /**
     * @OA\Get(
     *     path="/unidades/{id}",
     *     summary="Obtém os detalhes de um unidade",
     *     description="Retorna os detalhes de um unidade em específico pelo ID.",
     *     tags={"Unidade"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da unidade",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da unidade retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Unidade")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unidade não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unidade não encontrado!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar os unidades",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar os unidades."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        
        try {

            $unidade = $this->unidadeService->getUnidadeById($id);
            return new UnidadeResource($unidade);

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
     *     path="/unidades",
     *     summary="Cria um unidade",
     *     description="Registra um unidade no banco de dados.",
     *     tags={"Unidade"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados necessários para criar um unidade",
     *         @OA\JsonContent(
     *             required={"unid_nome","unid_sigla","cid_id","end_tipo_logradouro","end_logradouro","end_numero","end_bairro"},
     *             @OA\Property(property="unid_nome", type="string", example="Secretaria de Estadual de Educação"),
     *             @OA\Property(property="unid_sigla", type="string", example="SEEDUC"),
     *             @OA\Property(property="cid_id", type="integer", example="21"),
     *             @OA\Property(property="end_tipo_logradouro", type="string", example="Praça"),
     *             @OA\Property(property="end_logradouro", type="string", example="dos Três Poderes"),
     *             @OA\Property(property="end_numero", type="integer", example="1000"),
     *             @OA\Property(property="end_complemento", type="string", example="Quadra 5 lote 3"),
     *             @OA\Property(property="end_bairro", type="string", example="Horto Florestal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Unidade criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unidade criado com sucesso."),
     *             @OA\Property(property="data", ref="#/components/schemas/Unidade")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao criar o unidade.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Erro ao criar o unidade.")
     *         )
     *     )
     * )
     */
    public function store(StoreUnidadeRequest $request)
    {
        try {
            $unidade = $this->unidadeService->createUnidade($request->validated());

            return response()->json([
                'success'=> true,
                'data' => new UnidadeResource($unidade)
            ], Response::HTTP_CREATED);

        } catch (\DomainException $e) {
            return response()->json([
                'success'=> false,
                'message'=>$e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        
        } catch (\Exception $e) {
            return response()->json([
                'success'=> false,
                'message'=>  'Erro na unidade: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }   
    }

    /**
     * @OA\Put(
     *     path="/unidades/{id}",
     *     summary="Atualiza um unidade existente",
     *     description="Atualiza os dados de um unidade com base no ID fornecido.",
     *     tags={"Unidade"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do unidade a ser atualizado",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true, 
     *         description="Dados para atualização do unidade",
     *         @OA\JsonContent(
     *             required={"unid_nome","unid_sigla","cid_id","end_tipo_logradouro","end_logradouro","end_numero","end_bairro"},
     *             @OA\Property(property="unid_nome", type="string", example="Secretaria de Estadual de Educação"),
     *             @OA\Property(property="unid_sigla", type="string", example="SEEDUC"),
     *             @OA\Property(property="cid_id", type="integer", example="21"),
     *             @OA\Property(property="end_tipo_logradouro", type="string", example="Praça"),
     *             @OA\Property(property="end_logradouro", type="string", example="dos Três Poderes"),
     *             @OA\Property(property="end_numero", type="integer", example="1000"),
     *             @OA\Property(property="end_complemento", type="string", example="Quadra 5 lote 3"),
     *             @OA\Property(property="end_bairro", type="string", example="Horto Florestal")
     *         ),
     *             
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unidade atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unidade atualizado com sucesso!"),
     *             @OA\Property(property="data", ref="#/components/schemas/Unidade")
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
     *         description="Erro ao processar a atualização do unidade",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Erro ao processar a solicitação.")
     *         )
     *     )
     * )
     */
    public function update(UpdateUnidadeRequest $request, $id)
    {
        try {
            
            $unidade = $this->unidadeService->updateUnidade($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'data' => new UnidadeResource($unidade),
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
                'message' => 'Erro de atualização do unidade: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/unidades/{id}",
     *     summary="Exclui um unidade",
     *     description="Exclui um unidade do banco de dados com base no ID fornecido.",
     *     tags={"Unidade"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da unidade a ser excluído",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unidade excluído com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unidade excluído com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unidade não encontrada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unidade não encontrada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao excluir o unidade devido a dependências",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao deletar a unidade. Possivelmente há dependências associadas.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao excluir a unidade",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao excluir a unidade."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro.")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->unidadeService->deleteUnidade($id);

            return response()->json([
                'success' => true,
                'message' => 'Unidade excluída com sucesso.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return ApiResponse::handleException($e);
        }
    }
}

