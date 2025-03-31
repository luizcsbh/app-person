<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnderecoUnidadeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'servidor' => $this['servidor'],
            'unidade' => $this['unidade'],
            'endereco' => [
                'logradouro' => $this['endereco']->logradouro,
                'numero' => $this['endereco']->numero,
                'complemento' => $this['endereco']->complemento,
                'bairro' => $this['endereco']->bairro,
                'cidade' => $this['endereco']->cidade,
                'estado' => $this['endereco']->estado,
                'cep' => $this['endereco']->cep
            ]
        ];
    }
}