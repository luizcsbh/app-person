<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ServidorUnidadeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'nome' => $this->pes_nome,
            'idade' => Carbon::parse($this->pes_data_nascimento)->age,
            'unidade_lotacao' => $this->lotacaoAtiva->unidade->unid_nome,
            'fotografia' => $this->foto ? Storage::disk('minio')->url($this->foto->fp_arquivo) : null,
        ];
    }
}