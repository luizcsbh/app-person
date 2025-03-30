<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Servidor\Temporario\StoreServidorTemporarioRequest;
use App\Http\Requests\Servidor\Temporario\UpdateServidorTemporarioRequest;
use App\Http\Resources\ServidorTemporarioResource;
use App\Services\ServidorTemporarioService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ServidorTemporarioController extends Controller
{
    protected $servidorTemporarioService;

    public function __construct(ServidorTemporarioService $servidorTemporarioService)
    {
        $this->servidorTemporarioService = $servidorTemporarioService;
       
    }

    /**
     * @OA\Get(
     *     path="/servidores-temporarios",
     *     summary="Lista todos as servidores temporarios",
     *     description="Retorna uma lista de servidores temporarios armazenados no banco de dados.",
     *     tags={"Servidor Temporario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de servidores temporarios retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ServidorTemporarioFull"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhum servidor temporario encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Não há servidores temporarios cadastrados!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar os servidores temporarios",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar os servidores temporarios."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $servidoresTemporarios = $this->servidorTemporarioService->paginate($perPage);
    
            if ($servidoresTemporarios->total() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum servidor temporario encontrado!',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }
    
            return ServidorTemporarioResource::collection($servidoresTemporarios)
                ->additional([
                    'success' => true,
                    'message' => 'Lista de servidores temporarios recuperada com sucesso.'
                ],Response::HTTP_OK);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar servidores temporarios!',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        /**
     * @OA\Get(
     *     path="/servidores-temporarios/{id}",
     *     summary="Obtém os detalhes de um servidor temporario",
     *     description="Retorna os detalhes de um servidor temporario em específico pelo ID.",
     *     tags={"Servidor Temporario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da servidor temporario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da servidor temporario retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/ServidorTemporarioFull")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Servidor Temporario não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Servidor Temporario não encontrado!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar os servidores temporarios",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao buscar os servidores temporarios."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        
        try {

            $servidorTemporario = $this->servidorTemporarioService->getById($id);
            return new ServidorTemporarioResource($servidorTemporario);

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
     *     path="/servidores-temporarios",
     *     summary="Cria um servidor temporario",
     *     description="Registra um servidor temporario no banco de dados.",
     *     tags={"Servidor Temporario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados necessários para criar um servidor temporario",
     *         @OA\JsonContent(
     *             required={"pes_nome","pes_cpf","se_matricula","pes_data_nascimento","pes_sexo","pes_mae","pes_pai","cid_id","end_tipo_logradouro","end_logradouro","end_numero","end_bairro"},
     *             @OA\Property(property="pes_nome", type="string", example="João da Silva"),
     *             @OA\Property(property="pes_cpf", type="string", example="111.222.333-44"),
     *             @OA\Property(property="st_data_admissao", type="date", format="date", example="2023-06-10"),
     *             @OA\Property(property="pes_data_nascimento", type="date", format="date", example="1978-08-23"),
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
     *         description="Erro ao criar o servidor temporario.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Erro ao criar o servidor temporario.")
     *         )
     *     )
     * )
     */
    public function store(StoreServidorTemporarioRequest $request)
    {
        try {
            
            $servidor = $this->servidorTemporarioService->createServidorTemporario($request->validated());

            return response()->json([
                'success'=> true,
                'data' => new ServidorTemporarioResource($servidor)
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
     *     path="/servidores-temporarios/{id}",
     *     summary="Atualiza um servidor temporario existente",
     *     description="Atualiza os dados de uma servidor temporario com base no ID fornecido.",
     *     tags={"Servidor Temporario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da servidor temporario a ser atualizado",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Dados para atualização da servidor temporario",
     *         @OA\JsonContent(
     *             required={"pes_nome","pes_cpf","se_matricula","pes_data_nascimento","pes_sexo","pes_mae","pes_pai","cid_id","end_tipo_logradouro","end_logradouro","end_numero","end_bairro"},
     *             @OA\Property(property="pes_nome", type="string", example="João da Silva"),
     *             @OA\Property(property="pes_cpf", type="string", example="111.222.333-44"),
     *             @OA\Property(property="st_data_admissao", type="date", format="date", example="2023-06-10"),
     *             @OA\Property(property="pes_data_nascimento", type="date", format="date", example="1978-08-23"),
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
     *         response=200,
     *         description="Servidor Temporario atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Servidor Temporario atualizado com sucesso!"),
     *             @OA\Property(property="data", ref="#/components/schemas/ServidorTemporarioFull")
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
     *         description="Erro ao processar a atualização da servidor temporario",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Erro ao processar a solicitação.")
     *         )
     *     )
     * )
     */ 
    public function update(UpdateServidorTemporarioRequest $request, $id)
    {
        try {

            $servidor = $this->servidorTemporarioService->updateServidorTemporario($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'data' => new ServidorTemporarioResource($servidor),
                'message' => 'Dados atualizados com sucesso!'
            ], Response::HTTP_OK);
    
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registro não encontado!!'
            ], Response::HTTP_NOT_FOUND);
    
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na atualização: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/servidores-temporarios/{id}",
     *     summary="Exclui um servidor temporario",
     *     description="Exclui um servidor temporario do banco de dados com base no ID fornecido.",
     *     tags={"Servidor Temporario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da servidor temporario a ser excluído",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Servidor temporario excluído com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Servidor temporario excluído com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Servidor temporario não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Servidor temporario não encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao excluir o servidor temporario devido a dependências",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao deletar o servidor temporario. Possivelmente há dependências associadas.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao excluir o servidor temporario",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao excluir o servidor temporario."),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro.")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->servidorTemporarioService->deleteServidorTemporario($id);

            return response()->json([
                'success' => true,
                'message' => 'Servidor temporario excluído com sucesso.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return ApiResponse::handleException($e);
        }
    }
}

