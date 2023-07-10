<?php

namespace sistema\Controlador\Admin;

use sistema\Controlador\UsuarioControlador;
use sistema\Nucleo\Controlador;
use sistema\Nucleo\Helpers;
use sistema\Nucleo\Sessao;

class AdminControlador extends Controlador
{
    # atributo protegido usuario
    protected $usuario;

    # importando o twig template instanciado no Controlador
    public function __construct() {
        parent::__construct('templates/admin/views');

        # buscar o usuario da sessão através do método da classe UsuarioControlador que o recupera
        $this->usuario = UsuarioControlador::usuario();

        # se não existir usuario ou seu level não for 3
        if (!$this->usuario || $this->usuario->level != 3):
            $this->mensagem->mensagem(ERRO, 'Faça login para acessar o painel de controle');
            # limpa a chave id do usuário da sessão
            (new Sessao())->limpar('usuarioId');
            Helpers::renderizar('admin/login');
        endif;
    }    
}