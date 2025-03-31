<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ServidorEfetivoFull",
 *     type="object",
 *     @OA\Property(
 *         property="pessoa",
 *         ref="#/components/schemas/PessoaFull"
 *     ),
 *     @OA\Property(
 *         property="criado_em",
 *         type="string",
 *         example="24/03/2025 21:21"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="PessoaFull",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=22),
 *     @OA\Property(property="nome", type="string", example="João da Silva"),
 *     @OA\Property(property="matricula", type="string", example="2003456788467"),
 *     @OA\Property(property="Data de nascimento", type="string", format="date", example="1978-08-23"),
 *     @OA\Property(property="Cadastro de Pessoa Físsica", type="string", example="111.222.333-44"),
 *     @OA\Property(
 *         property="enderecos",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/EnderecoFull")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="EnderecoFull",
 *     type="object",
 *     @OA\Property(property="logradouro", type="string", example="Silviano Brandão"),
 *     @OA\Property(property="numero", type="string", example="1000"),
 *     @OA\Property(property="complemento", type="string", example="Bloco E, 50 apartamento 303"),
 *     @OA\Property(property="bairro", type="string", example="Horto Florestal"),
 *     @OA\Property(property="cidade_id", type="integer", example=21)
 * )
 */
class ServidorEfetivo extends Model
{
    use HasFactory;

    protected $primaryKey = 'pes_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'servidores_efetivos';

    protected $fillable = ['pes_id', 'se_matricula'];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }

    public function lotacao()
    {
        return $this->hasMany(Lotacao::class, 'pes_id');
    }
}
