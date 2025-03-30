<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServidorTemporarioResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'pessoa' => new PessoaResource($this->whenLoaded('pessoa')),
            'servidor_temporario' => [
                'data_admissao' => optional($this->st_data_admissao)->format('d/m/Y'),
                'data_demissao' => optional($this->st_data_demissao)->format('d/m/Y'),
                'criado_em' => $this->created_at->format('d/m/Y H:i'),
            ],
        ];
    }
}
