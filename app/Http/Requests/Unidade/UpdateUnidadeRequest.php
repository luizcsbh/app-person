<?php

namespace App\Http\Requests\Unidade;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnidadeRequest extends FormRequest

{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            // Regras para Pessoa
            'unid_nome' => 'sometimes|string|max:200|min:3',
            'unid_sigla' => 'sometimes|string|max:20|min:3',
    
            // Regras para Endereço
            'end_id' =>  'sometimes',
            'cid_id' => 'sometimes|exists:cidades,cid_id',
            'end_tipo_logradouro' => 'sometimes|string|max:50|min:3',
            'end_logradouro' => 'sometimes|string|max:200|min:3',
            'end_numero' => 'sometimes|integer|min:0',
            'end_complemento' => 'nullable|string|max:100',
            'end_bairro' => 'sometimes|string|max:100|min:3',
        ];
    }
    

    public function messages()
    {
        return [
            // Mensagens para Pessoa
            
            'unid_nome.string' => 'O nome deve ser um texto.',
            'unid_nome.max' => 'O nome pode ter no máximo 200 caracteres.',
            'unid_nome.min' => 'O nome deve ter no mínimo 3 caracteres.',

            'unid_sigla.string' => 'O nome deve ser um texto.',
            'unid_sigla.max' => 'O nome pode ter no máximo 20 caracteres.',
            'unid_sigla.min' => 'O nome deve ter no mínimo 3 caracteres.',

            // Mensagens para Endereço
          
            'cid_id.exists' => 'A cidade selecionada não existe',

            'end_tipo_logradouro.string' => 'O tipo de logradouro deve ser texto',
            'end_tipo_logradouro.max' => 'O tipo de logradouro não pode exceder 50 caracteres',
            'end_tipo_logradouro.min' => 'O tipo de logradouro deve ter pelo menos 3 caracteres',

            'end_logradouro.string' => 'O logradouro deve ser texto',
            'end_logradouro.max' => 'O logradouro não pode exceder 200 caracteres',
            'end_logradouro.min' => 'O logradouro deve ter pelo menos 3 caracteres',

            'end_numero.integer' => 'O número deve ser um valor inteiro',
            'end_numero.min' => 'O número não pode ser negativo',

            'end_complemento.string' => 'O complemento deve ser texto',
            'end_complemento.max' => 'O complemento não pode exceder 100 caracteres',

            'end_bairro.string' => 'O bairro deve ser texto',
            'end_bairro.max' => 'O bairro não pode exceder 100 caracteres',
            'end_bairro.min' => 'O bairro deve ter pelo menos 3 caracteres',
        ];
    }

    public function attributes(): array
    {
        return [
            'se_matricula' => 'matrícula',
        ];
    }
}