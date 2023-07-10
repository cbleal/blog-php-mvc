<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Helpers;

class AdminCategorias extends AdminControlador
{    
    public function index(): void
    {
        $categoria = new CategoriaModelo();

        echo $this->template->renderizar('categorias/index.html', [
            'total' => [
                'total'   => $categoria->totalCond(),
                'ativo'   => $categoria->totalCond("status = 1"),
                'inativo' => $categoria->totalCond("status = 0"),
            ],
            'categorias' => $categoria->busca()->ordem('id DESC')->resultado(true),
        ]);
    }

    public function cadastrar(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        if (isset($dados)):
            if ($this->checarDados($dados)):
                $categoria = new CategoriaModelo();
                $categoria->titulo = $dados['titulo'];
                $categoria->slug = Helpers::slugify($dados['titulo']);
                $categoria->texto = $dados['texto'];
                $categoria->status = $dados['status'];
    
                if ($categoria->salvar()) :
                    $this->mensagem->mensagem(SUCESSO, 'Categoria cadastrada com sucesso...')->flash();
                    Helpers::renderizar('admin/categorias/index');
                endif;
            endif;
        endif;

        echo $this->template->renderizar('categorias/formulario.html', []);
    }

    public function editar(int $id): void
    {
        $categoria = (new CategoriaModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)):
            if ($this->checarDados($dados)):
                $categoria = (new CategoriaModelo())->buscaPorId($id);
                $categoria->titulo = $dados['titulo'];
                $categoria->slug = Helpers::slugify($dados['titulo']);
                $categoria->texto = $dados['texto'];
                $categoria->status = $dados['status'];

                $categoria->atualizado_em = date('Y-m-d H:i:s');
    
                if ($categoria->salvar()) :
                    $this->mensagem->mensagem(SUCESSO, 'Categoria atualizada com sucesso...')->flash();
                    Helpers::renderizar('admin/categorias/index');
                endif;
            endif;

        endif;
        
        echo $this->template->renderizar('categorias/formulario.html', [
            'categoria' => $categoria,
        ]);
    }

    public function deletar(int $id)
    {
        $categoria = (new CategoriaModelo())->buscaPorId($id);
        # testar se o id é um número inteiro e se existe na tabela
        if (is_int($id)) :
            if (!$categoria) :
                $this->mensagem->mensagem(ATENCAO, 'Id informado não foi encontrado...')->flash();
                Helpers::renderizar('admin/categorias/index');
            endif;
        endif;

        if (!$categoria->apagar("id = {$id}")) :
            $this->mensagem->mensagem(ERRO, $categoria->erro())->flash();
            Helpers::renderizar('admin/categorias/index');
        endif;

        $categoria->apagar("id = {$id}");
        $this->mensagem->mensagem(SUCESSO, 'Categoria apagada com sucesso...')->flash();
        Helpers::renderizar('admin/categorias/index');
    }

    private function checarDados(array $dados): bool
    {                
        if (!Helpers::verificarInputs($dados)):
            $this->mensagem->mensagem(ERRO, 'Os campos são de preenchimento obrigatório...')->flash();
            return false;
        endif;              

        return true;
    }
}