<?php

namespace sistema\Controlador\Admin;

use sistema\Controlador\UsuarioControlador;
use sistema\Modelo\UsuarioModelo;
use sistema\Nucleo\Controlador;
use sistema\Nucleo\Helpers;

class AdminLogin extends Controlador
{
    public UsuarioModelo $usuario;

    # importando o twig template instanciado no Controlador
    public function __construct() {
        $this->usuario = new UsuarioModelo();
        parent::__construct('templates/admin/views');     
    }

    public function login(): void
    {     
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)):
            if ($this->checarDados($dados)):
                if ($this->usuario->login($dados)):
                    Helpers::renderizar('admin/dashboard');
                endif;          
            endif;
        endif;

        echo $this->template->renderizar('login.html', ['dados' => $dados]);
    }

    public function checarDados(array $dados): bool
    {
        if (empty($dados['email'])):
            $this->mensagem->mensagem(ERRO, 'Campo Email é obrigatório...')->flash();
            return false;
        endif;

        if (empty($dados['senha'])):           
            $this->mensagem->mensagem(ERRO, 'Campo Senha é obrigatório...')->flash();
            return false;
        endif;        

        return true;
    }
}