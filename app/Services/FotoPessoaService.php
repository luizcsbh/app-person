<?php

namespace App\Services;

use App\Repositories\FotoPessoaRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\InvalidFileException;
use Illuminate\Support\Facades\Storage;



class FotoPessoaService
{

    public function __construct(private FotoPessoaRepository $fotoPessoaRepository)
    {}

    /**
     * Armazena uma foto no MinIO e no banco de dados
     *
     * @param array $data Dados da foto (deve conter 'pes_id')
     * @param UploadedFile $file Arquivo de imagem
     * @return FotoPessoa
     * @throws \Exception
     */
    public function storeFoto(array $data, UploadedFile $file)
    {
       
        $this->validateFile($file);
        
        $filePath = 'fotos-pessoas/' . Str::uuid() . '.' . $file->extension();
    
        Storage::disk('minio')->put($filePath, file_get_contents($file->getRealPath()));
        
        return $this->fotoPessoaRepository->create([
            'fp_id' => Str::uuid(),
            'pes_id' => $data['pes_id'],
            'fp_arquivo' => $filePath,
            'fp_bucket' => 'minio',
            'fp_data' => now(),
            'ft_hash' => hash_file('sha256', $file->getRealPath())
        ]);
    }

    public function getFoto(string $id)
    {
        return $this->fotoPessoaRepository->getFotoById($id);
    }

    public function removeFoto(string $id)
    {
        return $this->fotoPessoaRepository->deleteFoto($id);
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