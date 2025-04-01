<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\Contracts\StorageServiceInterface;

class MinIOStorageService implements StorageServiceInterface
{
    public function upload(string $bucket, string $path, $file): string
    {
        // Garante que o caminho esteja correto
        $fullPath = trim("$bucket/$path", '/');
        
        // Verifica se é um UploadedFile ou caminho de arquivo
        $fileContents = $file instanceof UploadedFile
            ? file_get_contents($file->getRealPath())
            : file_get_contents($file);
        
        // Faz o upload com as configurações corretas
        Storage::disk('minio')->put(
            $fullPath,
            $fileContents,
            ['visibility' => 'public']
        );
        
        return $fullPath;
    }

    public function getUrl(string $bucket, string $path): string
    {
        $fullPath = trim("$bucket/$path", '/');
        
        if (!Storage::disk('minio')->exists($fullPath)) {
            throw new \RuntimeException("Arquivo não encontrado: $fullPath");
        }
        
        return Storage::disk('minio')->url($fullPath);
    }

    public function delete(string $bucket, string $path): bool
    {
        $fullPath = trim("$bucket/$path", '/');
        return Storage::disk('minio')->delete($fullPath);
    }
}