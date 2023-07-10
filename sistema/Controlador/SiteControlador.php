<?php

namespace sistema\Controlador;

use sistema\Modelo\PostModelo;
use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Controlador;
use sistema\Nucleo\Helpers;
use sistema\Suporte\Email;

class SiteControlador extends Controlador
{
    public function __construct() {
        parent::__construct('templates/site/views');
    }

    public function index(): void
    {
        # acessar o metodo renderizar de template herdado do controlador
        echo $this->template->renderizar('index.html', [
            'slides' => (new PostModelo)->busca("status = 1")->limite(3)->resultado(true),
            'posts'  => (new PostModelo)->busca("status = 1")->limite(10)->offset(3)->resultado(true),
            'categorias' => (new CategoriaModelo)->busca()->resultado(true),
        ]);
    }

    public function blog(): void
    {
        echo $this->template->renderizar('blog.html', [
            'titulo' => 'página blog',
            'conteudo' => 'blog'
        ]);
    }

    public function contato(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        // var_dump($dados);
        if (isset($dados)) {
            if (in_array('', $dados)) {
                $this->mensagem->mensagem(ATENCAO, 'Preencha todos os campos')->flash(); 
                Helpers::json('erro', 'preencha todos os campos');
            } elseif (!Helpers::validarEmail($dados['email'])) {
                Helpers::json('erro', 'email inválido');
            } else {
                try {
                    $email = new Email();

                    $view = $this->template->renderizar('emails/contato.html', [
                        'dados' => $dados,
                    ]);

                    $email->criar(
                        "Contato via site - " . SITE_NOME,
                        $view,
                        EMAIL_REMETENTE['email'],
                        EMAIL_REMETENTE['nome'],
                        $dados['email'],
                        $dados['nome'],
                    );

                    //Attachment files
                    $anexos = $_FILES['anexos'];
                    foreach ($anexos['tmp_name'] as $indice => $anexo) {
                        if (!$anexo == UPLOAD_ERR_OK) {
                            $email->anexar($anexo, $anexos['name'][$indice]);
                        }
                    }

                    //Attachment File
                    // if (!empty($_FILES['anexo'])) {
                    //     $anexo = $_FILES['anexo'];
                    //     $email->anexar($anexo['tmp_name'], $anexo['name']);
                    // }

                    $email->enviar(EMAIL_REMETENTE['email'], EMAIL_REMETENTE['nome']);

                    // $this->mensagem->mensagem(SUCESSO, 'E-mail enviado com sucesso')->flash();   
                    
                    // Helpers::json('redirecionar', Helpers::url());

                    Helpers::json('sucesso', 'E-mail enviado com sucesso');

                } catch (\PHPMailer\PHPMailer\Exception $e) {
                    $this->mensagem->mensagem(ERRO, $e->getMessage())->flash();
                }
            }
        }

        echo $this->template->renderizar('contato.html', [
            'titulo' => 'página contato',
        ]);
    }

    public function sobre(): void
    {
        echo $this->template->renderizar('sobre.html', [
            'titulo' => 'página sobre',
            'conteudo' => 'curso php 8'
        ]);
    }

    public function erro404(): void
    {
        echo $this->template->renderizar('404.html', [
            'titulo' => 'página erro',
            'conteudo' => 'página não encontrada'
        ]);
    }

    public function post(string $slug): void
    {
        $post = (new PostModelo)->buscaPorSlug($slug);

        if (! $post):
            Helpers::renderizar('404');
        endif;

        # incrementar o número de visita ao post
        $post->visitas += 1;

        # atualizar a data da última visita
        $post->ultima_visita_em = date('Y-m-d H:i:s');

        # salvar na tabela
        $post->salvar();

        echo $this->template->renderizar('post.html', [
            'titulo' => 'Post',
            'post' => $post,
            'categorias' => (new CategoriaModelo())->busca()->resultado(true),
        ]);
    }

    // public function post(int $id): void
    // {
    //     $post = (new PostModelo)->buscaPorId($id);

    //     if (! $post):
    //         Helpers::renderizar('404.html');
    //     endif;

    //     # incrementar o número de visita ao post
    //     $post->visitas += 1;

    //     # atualizar a data da última visita
    //     $post->ultima_visita_em = date('Y-m-d H:i:s');

    //     # salvar na tabela
    //     $post->salvar();

    //     echo $this->template->renderizar('post.html', [
    //         'titulo' => 'Post',
    //         'post' => $post,
    //         'categorias' => (new CategoriaModelo)->busca()->resultado(true),
    //     ]);
    // }

    // public function categoria(int $id): void
    // {
    //     $posts = (new PostModelo())->busca("categoria_id = {$id} AND status = 1")->resultado(true);
        
    //     if (! $posts):
    //         Helpers::renderizar('404.html');
    //     endif;

    //     echo $this->template->renderizar('categoria.html', [
    //         'titulo' => 'Categoria',
    //         'posts' => $posts,
    //         'categorias' => (new CategoriaModelo())->busca()->resultado(true),
    //     ]);
    // }

    public function categorias(): array
    {
        return (new CategoriaModelo())->busca("status = 1")->resultado(true);
    }

    public function categoria(string $slug): void
    {
        $categoria = (new CategoriaModelo())->buscaPorSlug($slug);
        
        if (! $categoria):
            Helpers::renderizar('404.html');
        endif;

        # incrementar o número de visita ao categoria
        $categoria->visitas += 1;

        # atualizar a data da última visita
        $categoria->ultima_visita_em = date('Y-m-d H:i:s');

        # salvar na tabela
        $categoria->salvar();

        echo $this->template->renderizar('categoria.html', [
            'titulo' => 'Categoria',
            'posts' => (new CategoriaModelo())->posts($categoria->id),
            'categorias' => $this->categorias(),
        ]);
    }

    public function buscar(): void
    {
        $busca = filter_input(INPUT_POST, 'busca', FILTER_DEFAULT);
        // $mensagem = '';

        // $posts = (new PostModelo)->pesquisa($busca);
        $posts = (new PostModelo)->busca("status = 1 AND titulo LIKE '%$busca%'")->resultado(true);
        // if (! $posts):
        //     // Helpers::renderizar('404.html');
        //     $mensagem = 'Nenhum resultado foi encontrado...';
        // endif;

        // echo $this->template->renderizar('busca.html', [
        //     'mensagem' => $mensagem,
        //     'posts' => $posts,
        //     'categorias' => (new CategoriaModelo)->ler(),
        // ]); 

        foreach ($posts as $post) {
            echo "<li class='list-group-item fw-bold'><a href=".Helpers::url('post/').$post->id.">".$post->titulo."</a></li>";            
        }
    }

}