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
            'bucket' => $this->fp_bucket,
            'fp_arquivo' => $this->fp_arquivo // Adicionado para referência
        ];
    }

    protected function getUrl()
    {
        try {
            $disk = Storage::disk($this->fp_bucket);
            
            // Verifica se o arquivo existe
            if (!$this->fileExists($disk)) {
                return null;
            }

            // Tenta gerar URL temporária para S3/MinIO
            if (config('filesystems.disks.'.$this->fp_bucket.'.driver') === 's3') {
                return $disk->temporaryUrl(
                    $this->fp_arquivo,
                    now()->addMinutes(30)
                );
            }

            // Fallback para URL pública
            return $disk->url($this->fp_arquivo);

        } catch (\Exception $e) {
            report($e); // Loga o erro sem quebrar a aplicação
            return null;
        }
    }

    protected function fileExists($disk)
    {
        try {
            return $disk->exists($this->fp_arquivo);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}