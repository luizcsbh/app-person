<?php

namespace App\Http\Requests\FotoPessoa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StoreFotoPessoaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

   /**
    * Valida o arquivo de imagem
    * 
    * @param UploadedFile $file
    * @throws ValidationException
    */
    protected function validateFile(UploadedFile $file)
    {
        $validator = Validator::make(['file' => $file], [
            'file' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:5120' // 5MB
            ]
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}