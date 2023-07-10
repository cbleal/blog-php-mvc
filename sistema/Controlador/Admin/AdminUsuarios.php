<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\UsuarioModelo;
use sistema\Nucleo\Helpers;

class AdminUsuarios extends AdminControlador
{
    private $user;

    public function __construct() {
        $this->user = new UsuarioModelo();
        parent::__construct();
    }
    public function index(): void
    {
        echo $this->template->renderizar('usuarios/index.html', [
            'total' => [
                'total'   => $this->user->totalCond(),
                'ativo'   => $this->user->totalCond("status = 1"),
                'inativo' => $this->user->totalCond("status = 0"),
            ],
            'usuarios' => $this->user->busca()->ordem('id DESC')->resultado(true),
        ]);
    }

    public function cadastrar(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) :
            if ($this->checarDados($dados)):
                // $usuario = new UsuarioModelo();
                $this->user->nome   = $dados['nome'];
                $this->user->email  = $dados['email'];
                $this->user->senha  = Helpers::gerarSenha($dados['senha']);
                $this->user->status = $dados['status'];
                $this->user->level  = $dados['level'];
    
                if ($this->user->salvar()) :
                    $this->mensagem->mensagem(SUCESSO, 'Usuário cadastrado com sucesso...')->flash();
                    Helpers::renderizar('admin/usuarios/index');
                endif;
            endif;
        endif;

        echo $this->template->renderizar('usuarios/formulario.html', ['usuario' => $dados]);
    }

    public function editar(int $id): void
    {
        $usuario = $this->user->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) :
            if ($this->checarDados($dados)):
                $usuario = $this->user->buscaPorId($id);
                $usuario->nome   = $dados['nome'];
                $usuario->email  = $dados['email'];
                $usuario->senha  = (empty($dados['senha']) ? $usuario->senha : Helpers::gerarSenha($dados['senha']));
                $usuario->status = $dados['status'];
                $usuario->level  = $dados['level'];
                
                $usuario->atualizado_em = date('Y-m-d H:i:s');
    
                if ($usuario->salvar()) :
                    $this->mensagem->mensagem(SUCESSO, 'Usuário atualizado com sucesso...')->flash();
                    Helpers::renderizar('admin/usuarios/index');
                endif;
            endif;
        endif;

        echo $this->template->renderizar('usuarios/formulario.html', ['usuario' => $usuario]);
    }

    public function deletar(int $id)
    {
        $usuario = $this->user->buscaPorId($id);
        # testar se o id é um número inteiro e se existe na tabela
        if (is_int($id)) :
            if (!$usuario) :
                $this->mensagem->mensagem(ATENCAO, 'Id informado não foi encontrado...')->flash();
                Helpers::renderizar('admin/usuarios/index');
            endif;
        endif;

        if (!$usuario->apagar("id = {$id}")) :
            $this->mensagem->mensagem(ERRO, $usuario->erro())->flash();
            Helpers::renderizar('admin/posts/index');
        endif;

        $usuario->apagar("id = {$id}");
        $this->mensagem->mensagem(SUCESSO, 'Usuário apagado com sucesso...')->flash();
        Helpers::renderizar('admin/usuarios/index');
    }

    private function checarDados(array $dados): bool
    {
        // if (in_array('', $dados)):
        //     $this->mensagem->mensagem(ERRO, 'Os campos são de preenchimento obrigatório...')->flash();
        //     return false;
        // endif;

        if (!Helpers::verificarInputs($dados)):
            $this->mensagem->mensagem(ERRO, 'Os campos são de preenchimento obrigatório...')->flash();
            return false;
        endif;   

        if (!Helpers::validarString($dados['nome'])):
            $this->mensagem->mensagem(ERRO, 'Campo Nome deve conter apenas letras...')->flash();
            return false;
        endif;

        if (!Helpers::validarEmail($dados['email'])):
            $this->mensagem->mensagem(ERRO, 'Campo Email é inválido...')->flash();
            return false;
        endif;

        if (!Helpers::validarSenha($dados['senha'])):
            $this->mensagem->mensagem(ERRO, 'Campo Senha deve conter entre 6 e 50 caracteres...')->flash();
            return false;
        endif;        

        return true;
    }
}
