<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Endereco",
 *     type="object",
 *     title="Endereço",
 *     description="Modelo de Endereço",
 *     required={"cid_id","end_tipo_logradouro","end_logradouro","end_numero","end_complemento","end_bairro"},
 *     @OA\Property(
 *         property="end_id",
 *         type="integer",
 *         description="ID do Endereço",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="cid_id",
 *         type="integer",
 *         description="ID da Cidade",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="end_tipo_logradouro",
 *         type="string",
 *         description="Descricão do tipo de logradouro",
 *         example="Avenida"
 *     ),
 *     @OA\Property(
 *         property="end_logradouro",
 *         type="string",
 *         description="Descricão do logradouro",
 *         example="Gustavo da Silveira"
 *     ),
 *     @OA\Property(
 *         property="end_numero",
 *         type="integer",
 *         description="Número do logradouro",
 *         example="1000"
 *     ),
 *     @OA\Property(
 *         property="end_complemento",
 *         type="string",
 *         description="Descricão do complemento do logradouro",
 *         example="Bloco E, 50 apartamento 303"
 *     ),
 *     @OA\Property(
 *         property="end_bairro",
 *         type="string",
 *         description="Bairro do logradouro",
 *         example="Horto Florestal"
 *     ),
 * )
 */
class Endereco extends Model
{
    use HasFactory;

    protected $primaryKey = 'end_id';
    public $incrementing = true;
    protected $keyType = 'string';
    protected $table = 'enderecos';

    protected $fillable = [
        'end_id',
        'cid_id',
        'end_tipo_logradouro',
        'end_logradouro',
        'end_numero',
        'end_complemento',
        'end_bairro',
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cid_id');
    }

    public function pessoas()
    {
        return $this->belongsToMany(Pessoa::class, 'pessoa_endereco', 'end_id', 'pes_id');
    }

    /*
    public function unidades()
    {
        return $this->belongsToMany(Unidade::class, 'unidade_endereco', 'end_id', 'pes_id');
    }
    */

}
