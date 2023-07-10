<?php

namespace sistema\Controlador;

use sistema\Modelo\UsuarioModelo;
use sistema\Nucleo\Controlador;
use sistema\Nucleo\Helpers;
use sistema\Nucleo\Sessao;

class UsuarioControlador extends Controlador
{
    # construtor que herda da classe pai Controlador que informa o caminho para as views
    public function __construct() {
        parent::__construct('templates/site/views');
    }    

    # método estático usuario()
    public static function usuario(): ?UsuarioModelo
    {
        # checar se existe sessão de usuário ativa
        $sessao = new Sessao();
        # se não existir, retorna nulo
        if (!$sessao->checar('usuarioId')):
            return null;
        endif;
        # se existir retorna um objeto usuario buscado pelo id
        return (new UsuarioModelo())->buscaPorId($sessao->usuarioId);
    }
}