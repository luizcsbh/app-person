<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Unidade",
 *     type="object",
 *     title="Unidade",
 *     description="Modelo da Unidade",
 *     required={"unid_nome","unid_sigla"},
 *     @OA\Property(
 *         property="unid_id",
 *         type="integer",
 *         description="ID da Unidade",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="unid_nome",
 *         type="string",
 *         description="Nome da unidade",
 *         example="Secretária de Planejamento"
 *     ),
 *     @OA\Property(
 *         property="unid_sigla",
 *         type="string",
 *         description="Sigla da unidade",
 *         example="SEPLAG"
 *     ),
 * )
 */
class Unidade extends Model
{
    use HasFactory;

    protected $primaryKey = 'unid_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'unidades';

    protected $fillable = [
        'unid_nome',
        'unid_sigla'
    ];

    public function enderecos()
    {
        return $this->belongsToMany(Endereco::class, 'unidade_endereco', 'unid_id', 'end_id');
    }

    public function lotacao()
    {
        return $this->belongsTo(Lotacao::class, 'unid_id');
    }
}
