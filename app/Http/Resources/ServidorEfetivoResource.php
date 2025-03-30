<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServidorEfetivoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'pessoa' => new PessoaResource($this->whenLoaded('pessoa')),
            'servidor_efetivo' => [
                'matricula' => $this->se_matricula,
                'criado_em' => $this->created_at->format('d/m/Y H:i'),
            ]
        ];
    }
}