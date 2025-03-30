<?php

namespace App\Exceptions;

use Exception;

class LotacaoAtivaException extends Exception
{
    // Status code HTTP para a resposta (opcional)
    protected $code = 422; // Unprocessable Entity
    
    // Mensagem padrão (opcional)
    public function __construct()
    {
        parent::__construct($message ?? 'Já existe uma lotação ativa para esta pessoa na unidade especificada. É necessário remover a lotação atual antes de criar uma nova.');
    }
    
    // Relatório da exceção (opcional)
    public function report()
    {
        // Lógica para registrar/logar a exceção
    }
    
    // Renderizar a exceção (opcional)
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => [
                'lotacao' => [$this->getMessage()]
            ]
        ], $this->code);
    }
}