<?php

namespace Tests\Feature;

use App\Models\FotoPessoa;
use App\Repositories\FotoPessoaRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FotoPessoaRepositoryTest extends TestCase
{
    public function testCreateFotoPessoa()
    {
    

        $data = [
            'pes_id' => '123e4567-e89b-12d3-a456-426614174000',
            'fp_arquivo' => 'test.jpg',
            'ft_hash' => md5('test')
        ];
        
        $foto = $this->fotoPessoaRepository->create($data);
        
        $this->assertInstanceOf(FotoPessoa::class, $foto);
        $this->assertEquals($data['pes_id'], $foto->pes_id);
        $this->assertNotNull($foto->fp_id);
    }
}
