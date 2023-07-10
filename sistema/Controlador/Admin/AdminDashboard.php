<?php

namespace sistema\Controlador\Admin;

use sistema\Nucleo\Helpers;
use sistema\Nucleo\Sessao;
use sistema\Controlador\UsuarioControlador;

class AdminDashboard extends AdminControlador
{
    public function dashboard(): void
    {
        // echo $this->usuario->nome;

        echo $this->template->renderizar('dashboard.html', []);
    }

    # método público sair() para limpar chave id do usuario da sessão
    public function sair(): void
    {
        # limpar a chave id do usuario da sessão
        $sessao = new Sessao();
        $sessao->limpar('usuarioId');

        # exibe mensagem de saída
        $this->mensagem->mensagem(ATENCAO, 'Você saiu do Painel de Controle')->flash();

        # redireciona para a página de login
        Helpers::renderizar('admin/login');
    }
}