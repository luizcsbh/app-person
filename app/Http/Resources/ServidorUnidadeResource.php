<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServidorUnidadeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'nome' => $this->pessoa->pes_nome,
            'idade' => $this->calculateAge($this->pessoa->pes_data_nascimento),
            'unidade_lotacao' => $this->unidade->unid_nome
        ];
    }

    private function calculateAge($birthDate)
    {
        $birthDate = new \DateTime($birthDate);
        $today = new \DateTime();
        return $today->diff($birthDate)->y;
    }
}