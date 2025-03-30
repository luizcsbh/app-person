<?php

namespace App\Repositories;

use App\Models\FotoPessoa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FotoPessoaRepository
{

    protected $model;

    public function __construct(FotoPessoa $fotoPessoa)
    {
        $this->model = $fotoPessoa;
    }

    public function storeFoto(array $data, $file)
    {
        
        $data['fp_id'] = Str::uuid()->toString();
        
        $filePath = 'fotos-pessoas/' . $data['fp_id'] . '.' . $file->extension();
        $fileContent = file_get_contents($file->getRealPath());
        
        Storage::disk(config('filesystems.default'))->put($filePath, $fileContent);
        
        $data['fp_bucket'] = config('filesystems.default');
        $data['fp_data'] = now();
        $data['ft_hash'] = hash_file('sha256', $file->getRealPath());
        
        return $this->model->create($data);
    }

    public function getFotoById(string $id)
    {
        return $this->model->findOrFail($id);
    }

    public function deleteFoto(string $id)
    {
        $foto = $this->getFotoById($id);
        
        // Remove o arquivo do storage
        Storage::disk($foto->fp_bucket)
            ->delete('fotos-pessoas/' . $foto->fp_id . '.' . pathinfo($foto->fp_path, PATHINFO_EXTENSION));
        
        return $foto->delete();
    }
}