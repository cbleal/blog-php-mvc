<?php

namespace sistema\Nucleo;

/**
 * Classe para apresentar uma mensagem
 * @author Claudinei B Leal <cborgesleal@gmail.com>
 */
class Mensagem
{
    private $texto;
    private $css;
    
    public function __toString()
    {
        return $this->renderizar();
    }

    /**
     * Método responsável pela apresentação das mensagens
     * @param string $tipo Tipo da mensagem
     * @param string $mensagem String que representa a mensagem
     * @return Mensagem Objeto da classe Mensagem
     */
    public function mensagem(string $tipo, string $mensagem): Mensagem
    {
        $this->texto = $this->filtrar($mensagem);
        $this->css = "alert alert-{$tipo}";
        return $this;
    }

    /**
     * Método responsável pela renderização das mensagens
     * @return string A mensagem filtrada
     */
    public function renderizar(): string
    {
        return "<div class='{$this->css} alert-dismissible fade show'>{$this->texto}<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }

    /**
     * Método responsável por filtrar uma string
     * @param string $mensagem Mensagem a ser filtrada
     * @return string Mensagem filtrada 
     */
    private function filtrar(string $mensagem): string
    {
        return filter_var(strip_tags($mensagem), FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function flash(): void
    {
        (new Sessao())->criar('flash', $this);
    }
    
}