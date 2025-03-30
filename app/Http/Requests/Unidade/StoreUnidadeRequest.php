<?php

namespace App\Http\Requests\Unidade;

use Illuminate\Foundation\Http\FormRequest;


class StoreUnidadeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Regras para Pessoa
            'unid_nome' => 'required|string|max:200|min:3',
            'unid_sigla' => 'required|string|max:20|min:3',

            // Regras para Endereço
            'cid_id' => 'required|exists:cidades,cid_id',
            'end_tipo_logradouro' => 'required|string|max:50|min:3',
            'end_logradouro' => 'required|string|max:200|min:3',
            'end_numero' => 'required|integer|min:0',
            'end_complemento' => 'nullable|string|max:100',
            'end_bairro' => 'required|string|max:100|min:3',
        ];
    }
    

    public function messages()
    {
        return [
            // Mensagens para Pessoa
            'unid_nome.required' => 'O nome é obrigatório.',
            'unid_nome.string' => 'O nome deve ser um texto.',
            'unid_nome.max' => 'O nome pode ter no máximo 200 caracteres.',
            'unid_nome.min' => 'O nome deve ter no mínimo 3 caracteres.',

            'unid_sigla.required' => 'O nome é obrigatório.',
            'unid_sigla.string' => 'O nome deve ser um texto.',
            'unid_sigla.max' => 'O nome pode ter no máximo 20 caracteres.',
            'unid_sigla.min' => 'O nome deve ter no mínimo 3 caracteres.',

            // Mensagens para Endereço
            'cid_id.required' => 'O campo cidade é obrigatório',
            'cid_id.exists' => 'A cidade selecionada não existe',

            'end_tipo_logradouro.required' => 'O tipo de logradouro é obrigatório',
            'end_tipo_logradouro.string' => 'O tipo de logradouro deve ser texto',
            'end_tipo_logradouro.max' => 'O tipo de logradouro não pode exceder 50 caracteres',
            'end_tipo_logradouro.min' => 'O tipo de logradouro deve ter pelo menos 3 caracteres',

            'end_logradouro.required' => 'O logradouro é obrigatório',
            'end_logradouro.string' => 'O logradouro deve ser texto',
            'end_logradouro.max' => 'O logradouro não pode exceder 200 caracteres',
            'end_logradouro.min' => 'O logradouro deve ter pelo menos 3 caracteres',

            'end_numero.required' => 'O número é obrigatório',
            'end_numero.integer' => 'O número deve ser um valor inteiro',
            'end_numero.min' => 'O número não pode ser negativo',

            'end_complemento.string' => 'O complemento deve ser texto',
            'end_complemento.max' => 'O complemento não pode exceder 100 caracteres',

            'end_bairro.required' => 'O bairro é obrigatório',
            'end_bairro.string' => 'O bairro deve ser texto',
            'end_bairro.max' => 'O bairro não pode exceder 100 caracteres',
            'end_bairro.min' => 'O bairro deve ter pelo menos 3 caracteres',
        ];
    }
}