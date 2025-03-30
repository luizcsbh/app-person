<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ServidorTemporarioFull",
 *     type="object",
 *     @OA\Property(
 *         property="pessoa",
 *         ref="#/components/schemas/PessoaTemp"
 *     ),
 *     @OA\Property(
 *         property="criado_em",
 *         type="string",
 *         example="24/03/2025 21:21"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="PessoaTemp",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=22),
 *     @OA\Property(property="nome", type="string", example="João da Silva"),
 *     @OA\Property(property="Data de admissão", type="string", format="date", example="2022-11-17"),
 *     @OA\Property(property="Data de demissão", type="string", format="date", example="2024-08-23"),
 *     @OA\Property(property="Data Nascimento", type="string", format="date", example="1978-08-23"),
 *     @OA\Property(
 *         property="enderecos",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/EnderecoTemp")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="EnderecoTemp",
 *     type="object",
 *     @OA\Property(property="logradouro", type="string", example="Silviano Brandão"),
 *     @OA\Property(property="numero", type="string", example="1000"),
 *     @OA\Property(property="complemento", type="string", example="Bloco E, 50 apartamento 303"),
 *     @OA\Property(property="bairro", type="string", example="Horto Florestal"),
 *     @OA\Property(property="cidade_id", type="integer", example=21)
 * )
 */
class ServidorTemporario extends Model
{
    use HasFactory;

    protected $primaryKey = 'pes_id';
    protected $table = 'servidores_temporarios';

    protected $fillable = ['pes_id', 'st_data_admissao', 'st_data_demissao'];

    protected $casts = [
        'st_data_admissao' => 'date:Y-m-d',
        'st_data_demissao' => 'date:Y-m-d',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }
}
