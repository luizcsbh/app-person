<?php

namespace App\Http\Requests\Lotacao;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLotacaoRequest extends FormRequest

{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'pes_id' => 'sometimes|exists:pessoas,pes_id',
            'unid_id' => 'sometimes|exists:unidades,unid_id',
            'lot_data_lotacao' => 'sometimes|date',
            'lot_data_remocao' => 'nullable|date',
            'lot_portaria' => 'sometimes|string|max:100|min:3',
        ];
    }
    

    public function messages()
    {
        return [
            
            'pes_id.exists' => 'O :attribute selecionada não existe.',

            'unid_id.exists' => 'O :attribute selecionada não existe.',

            'lot_data_lotacao.date' => 'A :attribute deve ser uma data válida.',

            'lot_data_remocao.date' => 'A :attribute deve ser uma data válida.',

            'lot_portaria.sometimes' => 'O :attribute é obrigatório.',
            'lot_portaria.string' => 'O :attribute deve ser um texto.',
            'lot_portaria.max' => 'O :attribute não pode ter mais de 100 caracteres.',
            'lot_portaria.min' => 'O :attribute deve ter pelo menos 3 caracteres.',
        ];
    }

    public function attributes()
    {
        return [
            'pes_id' => 'identificador da pessoa',
            'unid_id' => 'identificador da unidade',
            'lot_data_lotacao' => 'data de lotação',
            'lot_data_remocao' => 'data de remoção',
            'lot_portaria' => 'número da portaria',
        ];
    }
}