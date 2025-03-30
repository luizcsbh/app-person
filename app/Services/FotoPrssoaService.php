<?php

namespace App\Services;

use App\Repositories\FotoPessoaRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\InvalidFileException;
use Dotenv\Exception\InvalidFileException as ExceptionInvalidFileException;

class FotoPessoaService
{

    public function __construct(private FotoPessoaRepository $repository)
    {}

    public function uploadFoto(array $data, UploadedFile $file)
    {
        $this->validateFile($file);
        
        return $this->repository->storeFoto($data, $file);
    }

    public function getFoto(string $id)
    {
        return $this->repository->getFotoById($id);
    }

    public function removeFoto(string $id)
    {
        return $this->repository->deleteFoto($id);
    }

    protected function validateFile(UploadedFile $file)
    {
        $validator = Validator::make(
            ['foto' => $file],
            [
                'foto' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048' // 2MB
                ]
            ]
        );

        if ($validator->fails()) {
            throw new InvalidFileException($validator->errors()->first());
        }
    }
}