<?php

namespace App\Http\Controllers;


use App\Services\FotoPessoaService;
use Illuminate\Http\Response;
use App\Http\Resources\FotoPessoaResource;

class FotoPessoaController extends Controller
{
    protected $service;

    public function __construct(FotoPessoaService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Post(
     *     path="/api/fotos-pessoa",
     *     summary="Upload de foto de pessoa",
     *     tags={"Fotos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"pes_id", "foto"},
     *                 @OA\Property(property="pes_id", type="string", example="uuid"),
     *                 @OA\Property(property="foto", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Foto armazenada com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store( $request)
    {
        try {
            $foto = $this->service->storeFoto(
                $request->only('pes_id'),
                $request->file('foto')
            );

            return response()->json(
                new FotoPessoaResource($foto),
                Response::HTTP_CREATED
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar foto: ' . $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/fotos-pessoa/{id}",
     *     summary="Obter foto de pessoa",
     *     tags={"Fotos"},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Dados da foto"),
     *     @OA\Response(response=404, description="Foto não encontrada")
     * )
     */
    public function show(string $id)
    {
        try {
            $foto = $this->service->getFoto($id);
            return new FotoPessoaResource($foto);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Foto não encontrada'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/fotos-pessoa/{id}",
     *     summary="Remover foto de pessoa",
     *     tags={"Fotos"},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=204, description="Foto removida"),
     *     @OA\Response(response=404, description="Foto não encontrada")
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->service->removeFoto($id);
            return response()->noContent();
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover foto: ' . $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}