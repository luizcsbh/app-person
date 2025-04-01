<?php

namespace App\Http\Controllers;

use App\Http\Requests\FotoPessoa\StoreFotoPessoaRequest;
use App\Repositories\FotoPessoaRepository;
use App\Services\FotoPessoaService;
use Illuminate\Http\JsonResponse;

class FotoPessoaController extends Controller
{
   
    public function __construct(
        private FotoPessoaService $fotoPessoaService,
        private FotoPessoaRepository $fotoPessoaRepository
    ){}

     /**
     * @OA\Post(
     *     path="/fotos-pessoas",
     *     summary="Upload de foto para uma pessoa",
     *     tags={"Foto Pessoa"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"pes_id", "foto"},
     *                 @OA\Property(
     *                     property="pes_id",
     *                     type="integer",
     *                     description="ID da pessoa",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="foto",
     *                     type="string",
     *                     format="binary",
     *                     description="Arquivo de imagem (JPEG, PNG, GIF)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Foto cadastrada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="foto",
     *                     ref="#/components/schemas/FotoPessoa"
     *                 ),
     *                 @OA\Property(
     *                     property="url",
     *                     type="string",
     *                     example="http://minio:9000/bucket/pessoas/abc123.jpg"
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Foto cadastrada com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro no upload",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Esta imagem já foi cadastrada anteriormente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação falhou",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O campo pes_id é obrigatório."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="pes_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="O campo pes_id é obrigatório.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreFotoPessoaRequest $request): JsonResponse
    {
        try {
            $resultado = $this->fotoPessoaService->uploadFotoPessoa(
                $request->pes_id,
                $request->file('foto')
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Foto cadastrada com sucesso.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

     /**
     * @OA\Get(
     *     path="/fotos-pessoas/{pesId}",
     *     summary="Obter foto de uma pessoa",
     *     tags={"Foto Pessoa"},
     *     @OA\Parameter(
     *         name="pesId",
     *         in="path",
     *         required=true,
     *         description="ID da pessoa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Foto encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/FotoPessoa"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Foto não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Foto não encontrada.")
     *         )
     *     )
     * )
     */
    public function show(int $pesId): JsonResponse
    {
        $foto = $this->fotoPessoaRepository->buscarPorPessoa($pesId);

        if (!$foto) {
            return response()->json([
                'success' => false,
                'message' => 'Foto não encontrada.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $foto
        ]);
    }
}