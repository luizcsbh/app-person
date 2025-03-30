<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;  

class FotoPessoaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'fp_id' => $this->fp_id,
            'pes_id' => $this->pes_id,
            'fp_data' => $this->fp_data->toDateTimeString(),
            'url' => $this->getUrl(),
            'hash' => $this->ft_hash,
            'bucket' => $this->fp_bucket
        ];
    }

    protected function getUrl()
    {
        // Obtém a extensão do arquivo (assumindo que o nome do arquivo contém a extensão)
        $extension = pathinfo($this->fp_arquivo, PATHINFO_EXTENSION);
        
        // Constrói o caminho completo do arquivo
        $filePath = "fotos-pessoas/{$this->fp_id}.{$extension}";
        
        // Retorna a URL assinada (válida por tempo limitado)
        if (method_exists(Storage::disk($this->fp_bucket), 'temporaryUrl')) {
            return Storage::disk($this->fp_bucket)->temporaryUrl(
                $filePath,
                now()->addMinutes(5)
            );
        }

        // Fallback for storage drivers that do not support temporaryUrl
        return Storage::disk($this->fp_bucket)->url($filePath);
    }
}