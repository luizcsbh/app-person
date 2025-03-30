<?php

namespace App\Http\Requests\FotoPessoa;

use Illuminate\Foundation\Http\FormRequest;

class StoreFotoPessoaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pes_id' => 'required|uuid|exists:pessoas,pes_id',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }
}