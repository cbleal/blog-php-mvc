<?php

namespace sistema\Nucleo;

use sistema\Suporte\Template;
use sistema\Nucleo\Mensagem;

class Controlador
{
    protected Template $template;
    protected Mensagem $mensagem;

    public function __construct(string $diretorio) 
    {
        $this->template = new Template($diretorio);

        $this->mensagem = new Mensagem();
    }
}