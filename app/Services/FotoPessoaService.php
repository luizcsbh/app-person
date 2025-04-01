<?php

namespace App\Services;

use App\Repositories\Contracts\FotoPessoaRepositoryInterface;
use App\Services\Contracts\StorageServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FotoPessoaService
{
    public function __construct(
        private FotoPessoaRepositoryInterface $repository,
        private StorageServiceInterface $storageService
    ) {}

    public function uploadFotoPessoa(int $pesId, UploadedFile $foto): array
    {
        
        // Gerar hash único para a imagem
        $hash = hash_file('sha256', $foto->path());
       
        // Verificar se já existe uma foto com o mesmo hash
        if ($this->repository->buscarPorHash($hash)) {
            throw new \Exception('Esta imagem já foi cadastrada anteriormente.');
        }

        // Gerar nome do arquivo
        $nomeArquivo = $this->gerarNomeArquivo($foto, $hash);
       
        $bucket = config('filesystems.disks.minio.bucket');
        
        // Fazer upload para o MinIO
        $caminhoArquivo = $this->storageService->upload(
            $bucket,
            $nomeArquivo,
            $foto
        );

       
        // Salvar metadados no banco
        $fotoPessoa = $this->repository->criar([
            'pes_id' => $pesId,
            'fp_data' => now()->toDateString(),
            'fp_arquivo' => $nomeArquivo,
            'fp_bucket' => $bucket,
            'fp_hash' => $hash,
        ]);

        return [
            'foto' => $fotoPessoa,
            'url' => $this->storageService->getUrl($bucket, $nomeArquivo)
        ];
    }

    private function gerarNomeArquivo(UploadedFile $foto, string $hash): string
    {
        $extensao = $foto->getClientOriginalExtension();
        return "pessoas/{$hash}.{$extensao}";
    }
}