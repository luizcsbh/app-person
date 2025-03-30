<?php

namespace App\Repositories;

use App\Exceptions\DatabaseException;
use App\Models\FotoPessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    /**
     * Cria um novo registro de foto de pessoa
     *
     * @param array $data Dados da foto
     * @return \App\Models\FotoPessoa
     * @throws \App\Exceptions\DatabaseException
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $foto = $this->model->create([
                'fp_id' => $data['fp_id'] ?? Str::uuid()->toString(),
                'pes_id' => $data['pes_id'],
                'fp_arquivo' => $data['fp_arquivo'],
                'fp_bucket' => $data['fp_bucket'] ?? 'minio',
                'fp_data' => $data['fp_data'] ?? now(),
                'ft_hash' => $data['ft_hash']
            ]);

            DB::commit();

            return $foto;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar foto de pessoa', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new DatabaseException('Erro ao salvar foto no banco de dados');
        }
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