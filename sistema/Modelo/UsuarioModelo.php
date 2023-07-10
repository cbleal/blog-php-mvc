<?php

namespace sistema\Modelo;

use sistema\Nucleo\Helpers;
use sistema\Nucleo\Modelo;
use sistema\Nucleo\Sessao;

/**
 * Classe que representa a tabela de usuarios do banco de dados
 * Ela extende da superclasse Modelo que contém métodos de 
 * manipulação da tabela.
 */
class UsuarioModelo extends Modelo
{
    /**
     * Construtor herdado da superclasse Modelo e passando atributos da
     * tabela para o memso.
     */
    public function __construct()
    {
        parent::__construct('usuarios');
    }

    /**
     * Método responsável por buscar um usuario pelo campo email
     * @param string $email Email a ser buscado na tabela
     */
    public function buscaPorEmail(string $email): ?UsuarioModelo
    {
        # utiliza o método busca da superclasse
        $busca = $this->busca("email = :e", "e={$email}");

        # retorna um objeto do tipo usuario
        return $busca->resultado();
    }

    /**
     * Método responsável por administrar o login do usuario
     */
    public function login(array $dados): bool
    {
        $usuario = (new UsuarioModelo())->buscaPorEmail($dados['email']);

        # validação email e senha
        if (!$usuario || $usuario->senha != Helpers::verificarSenha( $dados['senha'], $usuario->senha)):
            $this->mensagem->mensagem(ERRO, "Dados ncorretos...")->flash();
            return false;
        endif;

        # validação status
        if ($usuario->status != 1):           
            $this->mensagem->mensagem(ERRO, 'Favor, ative sua conta...')->flash();
            return false;
        endif;

        # validação level
        if ($usuario->level < 3):           
            $this->mensagem->mensagem(ERRO, 'Usuário sem permissão...')->flash();
            return false;
        endif;

        # inserir valor do último login do usuário
        $usuario->ultimo_login = date('Y-m-d H:i:s');
        $usuario->salvar();

        # criar sessão com id do usuario
        (new Sessao())->criar('usuarioId', $usuario->id);

        // $this->mensagem->mensagem(SUCESSO, "{$usuario->nome}, Seja Bem-Vindo ao Painel de Controle")->flash();

        return true;
    }

    public function salvar(): bool
    {
        if ($this->busca("email = :e AND id != :id", "e={$this->email}&id={$this->id}")->resultado()):
            $this->mensagem->mensagem(ATENCAO, "O email {$this->email} já está cadastrado.")->flash();
            return false;
        endif;

        parent::salvar();

        return true;
    }
}
