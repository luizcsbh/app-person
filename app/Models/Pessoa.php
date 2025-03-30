<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Pessoa",
 *     type="object",
 *     title="Pessoa",
 *     description="Modelo de Pessoa",
 *     required={"pes_nome","pes_cpf","pes_data_nascimento","pes_sexo","pes_mae","pes_pai"},
 *     @OA\Property(
 *         property="pes_id",
 *         type="integer",
 *         description="ID do Pessoa",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="pes_nome",
 *         type="string",
 *         description="Nome da pessoa",
 *         example="João da Silva"
 *     ),
 *     @OA\Property(
 *         property="pes_cpf",
 *         type="string",
 *         description="CPF da pessoa",
 *         example="111.222.333-44"
 *     ),
 *     @OA\Property(
 *         property="pes_data_nascimento",
 *         type="datetime",
 *         description="Data de nascimento",
 *         example="1978-08-23"
 *     ),
 *     @OA\Property(
 *         property="pes_sexo",
 *         type="string",
 *         description="Sexo da pessoa",
 *         example="Masculino"
 *     ),
 *     @OA\Property(
 *         property="pes_mae",
 *         type="string",
 *         description="Nome da mãe",
 *         example="Maria Aparecida da Silva"
 *     ),
 *     @OA\Property(
 *         property="pes_pai",
 *         type="string",
 *         description="Nome da pai",
 *         example="Cícero Joaquim da Silva"
 *     ),
 * )
 */
class Pessoa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pessoas';
    protected $primaryKey = 'pes_id'; 
    public $incrementing = true; 
    protected $keyType = 'string';

    protected $casts = [
        'pes_data_nascimento' => 'date:Y-m-d', // Formato específico
        'created_at' => 'datetime:Y-m-d H:i:s', // Data e hora
    ];

    protected $fillable = [
        'pes_id',
        'pes_nome',
        'pes_cpf',
        'pes_data_nascimento',
        'pes_sexo',
        'pes_mae',
        'pes_pai',
    ];

    public function enderecos()
    {
        return $this->belongsToMany(Endereco::class, 'pessoa_endereco', 'pes_id', 'end_id');
    }

    public function servidorEfetivo()
    {
        return $this->hasOne(ServidorEfetivo::class, 'pes_id');
    }

    public function servidorTemporario()
    {
        return $this->hasOne(ServidorTemporario::class, 'pes_id');
    }

    public function lotacao()
    {
        return $this->hasOne(Lotacao::class, 'pes_id');
    }

    public function foto()
    {
        return $this->hasOne(FotoPessoa::class, 'pes_id');
    }

}
