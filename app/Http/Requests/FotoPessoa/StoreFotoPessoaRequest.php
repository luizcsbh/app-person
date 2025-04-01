<?php

namespace App\Http\Requests\FotoPessoa;

use Illuminate\Foundation\Http\FormRequest;

class StoreFotoPessoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pes_id' => 'required|integer|exists:pessoas,pes_id',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }
}