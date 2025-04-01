<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="FotoPessoa",
 *     type="object",
 *     required={"fp_id", "pes_id", "fp_hash"},
 *     @OA\Property(property="fp_id", type="integer", example=1),
 *     @OA\Property(property="pes_id", type="integer", example=1),
 *     @OA\Property(property="fp_data", type="string", format="date", example="2023-01-01"),
 *     @OA\Property(property="fp_arquivo", type="string", example="pessoas/abc123.jpg"),
 *     @OA\Property(property="fp_bucket", type="string", example="laravel-app"),
 *     @OA\Property(property="fp_hash", type="string", example="e608dea6de582b3e1c24cb9556a2fd008a7a8e701559192384605a55794a002e"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class FotoPessoa extends Model
{
    use HasFactory;

    protected $primaryKey = 'fp_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'foto_pessoa';

    protected $fillable = [
        'fp_id',
        'pes_id',
        'fp_data',
        'fp_arquivo',
        'fp_bucket',
        'fp_hash'
    ];

    protected $casts = [
        'fp_data' => 'date',
    ];


    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }

    public function getUrlCompletaAttribute()
    {
        if (!$this->caminho) {
            return null;
        }

        return config('filesystems.disks.minio.url') . '/' . 
               config('filesystems.disks.minio.bucket') . '/' . 
        $this->caminho;
    }
    
}
