<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Lotacao",
 *     type="object",
 *     title="Lotação",
 *     description="Modelo de Lotação",
 *     required={"pes_id","unid_id","lot_data_lotacao","lot_data_remocao","lot_portaria"},
  *     @OA\Property(
 *         property="lot_id",
 *         type="integer",
 *         description="ID do Lotação",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="pes_id",
 *         type="integer",
 *         description="ID da Pessoa",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="unid_id",
 *         type="integer",
 *         description="ID da Unidade",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="lot_data_lotacao",
 *         type="datetime",
 *         description="Data da lotação",
 *         example="2021-05-01"
 *     ),
 *     @OA\Property(
 *         property="lot_data_remocao",
 *         type="datetime",
 *         description="Datada remoção",
 *         example="2024-10-15"
 *     ),
 *     @OA\Property(
 *         property="lot_portaria",
 *         type="string",
 *         description="Portaria",
 *         example="Portaria 23.0001/24"
 *     ),
 * )
 */
class Lotacao extends Model
{
    use HasFactory;

   
    protected $primaryKey = 'lot_id';
    public $incrementing = true;
    protected $keyType = 'string';
    protected $table = 'lotacoes';

    protected $fillable = [
        'lot_id',
        'pes_id',
        'unid_id',
        'lot_data_lotacao',
        'lot_data_remocao',
        'lot_portaria'
    ];

    protected $casts = [
        'lot_data_lotacao' => 'date:Y-m-d',
        'lot_data_remocao' => 'date:Y-m-d',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }

    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'unid_id');
    }
}
