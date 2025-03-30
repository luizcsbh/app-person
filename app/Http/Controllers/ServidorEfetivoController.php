<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Servidor\Efetivo\StoreServidorEfetivoRequest;
use App\Http\Requests\Servidor\Efetivo\UpdateServidorEfetivoRequest;
use App\Http\Resources\ServidorEfetivoResource;
use App\Services\ServidorEfetivoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ServidorEfetivoController extends Controller
{
    protected $servidorEfetivoService;

    public function __construct(ServidorEfetivoService $servidorEfetivoService)
    {
        $this->servidorEfetivoService = $servidorEfetivoService;
       
    }

    /**
     * @OA\Get(
     *     path="/servidores-efetivos",
     *     summary="Lista todos as servidores efetivos",
     *     description="Retorna uma lista de servidores efetivos armazenados no banco de dados.",
     *     tags={"Servidor Efetivo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de servidores efetivos retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ServidorEfetivoFull"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhum servidor efetivo encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Não há servidores efetivos cadastrados!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar os servidores efetivos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar os servidores efetivos."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $servidoresEfetivos = $this->servidorEfetivoService->paginate($perPage);
    
            if ($servidoresEfetivos->total() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum servidor efetivo encontrada',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }
    
            return ServidorEfetivoResource::collection($servidoresEfetivos)
                ->additional([
                    'success' => true,
                    'message' => 'Lista de servidores efetivos recuperada com sucesso'
                ],Response::HTTP_OK);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar servidores efetivos',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        /**
     * @OA\Get(
     *     path="/servidores-efetivos/{id}",
     *     summary="Obtém os detalhes de um servidor efetivo",
     *     description="Retorna os detalhes de um servidor efetivo em específico pelo ID.",
     *     tags={"Servidor Efetivo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da servidor efetivo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da servidor efetivo retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/ServidorEfetivoFull")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Servidor Efetivo não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Servidor Efetivo não encontrado!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar os servidores efetivos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar os servidores efetivos."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        
        try {

            $servidorEfetivo = $this->servidorEfetivoService->getById($id);
            return new ServidorEfetivoResource($servidorEfetivo);

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
     *     path="/servidores-efetivos",
     *     summary="Cria um servidor efetivo",
     *     description="Registra um servidor efetivo no banco de dados.",
     *     tags={"Servidor Efetivo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados necessários para criar um servidor efetivo",
     *         @OA\JsonContent(
     *             required={"pes_nome","pes_cpf","se_matricula","pes_data_nascimento","pes_sexo","pes_mae","pes_pai","cid_id","end_tipo_logradouro","end_logradouro","end_numero","end_bairro"},
     *             @OA\Property(property="pes_nome", type="string", example="João da Silva"),
     *             @OA\Property(property="pes_cpf", type="string", example="111.222.333-44"),
     *             @OA\Property(property="se_matricula", type="string", example="2003456788467"),
     *             @OA\Property(property="pes_data_nascimento", type="datetime", format="datetime", example="1978-08-23T12:00:00Z"),
     *             @OA\Property(property="pes_sexo", type="string", example="Masculino"),
     *             @OA\Property(property="pes_mae", type="string", example="Maria Aparecida da Silva"),
     *             @OA\Property(property="pes_pai", type="string", example="Cícero Joaquim da Silva"),
     *             @OA\Property(property="cid_id", type="integer", example="21"),
     *             @OA\Property(property="end_tipo_logradouro", type="string", example="Avenida"),
     *             @OA\Property(property="end_logradouro", type="string", example="Silviano Brandão"),
     *             @OA\Property(property="end_numero", type="integer", example="1000"),
     *             @OA\Property(property="end_complemento", type="string", example="Bloco E, 50 apartamento 303"),
     *             @OA\Property(property="end_bairro", type="string", example="Horto Florestal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pessoa criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Pessoa criado com sucesso."),
     *             @OA\Property(property="data", ref="#/components/schemas/Pessoa")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao criar o servidor efetivo.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Erro ao criar o servidor efetivo.")
     *         )
     *     )
     * )
     */
    public function store(StoreServidorEfetivoRequest $request)
    {
        try {
            $servidor = $this->servidorEfetivoService->createServidorEfetivo($request->validated());

            return response()->json([
                'success'=> true,
                'data' => new ServidorEfetivoResource($servidor)
            ], Response::HTTP_CREATED);

        } catch (\DomainException $e) {
            return response()->json([
                'success'=> false,
                'message'=>$e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        
        } catch (\Exception $e) {
            return response()->json([
                'success'=> false,
                'message'=>  'Erro no servidor: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }   
    }

  /**
 * @OA\Put(
 *     path="/servidores-efetivos/{id}",
 *     summary="Atualiza um servidor efetivo existente",
 *     description="Atualiza os dados de um servidor efetivo com base no ID fornecido.",
 *     tags={"Servidor Efetivo"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID do servidor efetivo a ser atualizado",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true, 
 *         description="Dados para atualização do servidor efetivo",
 *         @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="pes_nome", type="string", example="Fulano atualizado"),
 *              @OA\Property(property="pes_data_nascimento", type="string", format="date", example="2001-01-03"),
 *              @OA\Property(property="pes_sexo", type="string", example="Masculino"),
 *              @OA\Property(property="pes_mae", type="string", example="Maria da Silva"),
 *              @OA\Property(property="pes_pai", type="string", example="José da Silva"),
 *              @OA\Property(property="end_id", type="integer", example=19),
 *              @OA\Property(property="end_logradouro", type="string", example="Rua Atualizada 123"),
 *              @OA\Property(property="end_numero", type="string", example="456"),
 *              @OA\Property(property="end_tipo_logradouro", type="string", example="Rua"),
 *              @OA\Property(property="cid_id", type="integer", example=21),
 *              @OA\Property(property="end_complemento", type="string", example="Bloco E"),
 *              @OA\Property(property="end_bairro", type="string", example="Centro")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Servidor Efetivo atualizado com sucesso",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Servidor Efetivo atualizado com sucesso!"),
 *             @OA\Property(property="data", ref="#/components/schemas/ServidorEfetivoFull")
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
 *         description="Erro ao processar a atualização do servidor efetivo",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Erro ao processar a solicitação.")
 *         )
 *     )
 * )
 */
    public function update(UpdateServidorEfetivoRequest $request, $id)
    {
        try {
            
            $servidor = $this->servidorEfetivoService->updateServidorEfetivo($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'data' => new ServidorEfetivoResource($servidor),
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
                'message' => 'Erro de atualização do servidor efetivo: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/servidores-efetivos/{id}",
     *     summary="Exclui um servidor efetivo",
     *     description="Exclui um servidor efetivo do banco de dados com base no ID fornecido.",
     *     tags={"Servidor Efetivo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da servidor efetivo a ser excluído",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Servidor efetivo excluído com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Servidor efetivo excluído com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Servidor efetivo não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Servidor efetivo não encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao excluir o servidor efetivo devido a dependências",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao deletar o servidor efetivo. Possivelmente há dependências associadas.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao excluir o servidor efetivo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao excluir o servidor efetivo."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro.")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->servidorEfetivoService->deleteServidorEfetivo($id);

            return response()->json([
                'success' => true,
                'message' => 'Servidor efetivo excluída com sucesso.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return ApiResponse::handleException($e);
        }
    }
}

