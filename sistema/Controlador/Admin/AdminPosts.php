<?php

namespace sistema\Controlador\Admin;

// use sistema\Biblioteca\Upload;
use sistema\Modelo\CategoriaModelo;
use sistema\Modelo\PostModelo;
use sistema\Nucleo\Helpers;
use Verot\Upload\Upload;

class AdminPosts extends AdminControlador
{
    private string $capa;

    public function datatable(): void
    {
        $datatable = $_REQUEST;
        $datatable = filter_var_array($datatable, FILTER_SANITIZE_SPECIAL_CHARS);

        $limite = $datatable['length'];
        $offset = $datatable['start'];
        $busca  = $datatable['search']['value'];
        $colunas = [
            0 => 'id',
            1 => 'titulo'
        ];
        $ordem = " " . $colunas[$datatable['order'][0]['column']] . " ";
        $ordem .= " " . $datatable['order'][0]['dir'] . " ";

        $posts  = new PostModelo();

        if (empty($busca)) {
            $posts->busca()->ordem($ordem)->limite($limite)->offset($offset);
            $total = (new PostModelo())->busca(null, "COUNT(id)", "id")->total();
        } else {
            $posts->busca("id LIKE '%{$busca}%' OR titulo LIKE '%{$busca}%' ")->limite($limite)->offset($offset);
            $total = $posts->total();
        }

        $dados = [];
        foreach ($posts->resultado(true) as $post) {
            $dados[] = [
                $post->id,
                $post->titulo,
                $post->status,
            //     '<a href="' . Helpers::url('admin/posts/editar/' . $post->id) . '" title="Alterar Registro" class="m-1"><i
            // class="fa-solid fa-pen"></i></a> <a href="' . Helpers::url('admin/posts/deletar/' . $post->id) . '" title="Excluir Registro"
            // class="m-1 text-danger"><i class="fa-solid fa-trash"></i></a>',
            ];
        }
        $retorno = [
            "draw" => $datatable['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $dados,
        ];
        echo json_encode($retorno);
    }

    public function index(): void
    {
        $post = new PostModelo();
        echo $this->template->renderizar('posts/index.html', [
            'posts' => [
                // 'posts' => $post->busca()->ordem("id DESC")->resultado(true),
                'total'   => $post->totalCond(),
                'ativo'   => $post->totalCond("status = 1"),
                'inativo' => $post->totalCond("status = 0"),
            ],            
        ]);
    }

    public function cadastrar(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) :
            if ($this->checarDados($dados)) :
                $post = new PostModelo();
                $post->categoria_id = $dados['categoria_id'];
                $post->usuario_id = $this->usuario->id;
                $post->titulo = $dados['titulo'];
                $post->slug = Helpers::slugify($dados['titulo']);
                $post->texto  = $dados['texto'];
                $post->status = $dados['status'];

                $post->capa = $this->capa;

                if ($post->salvar()) :
                    $this->mensagem->mensagem(SUCESSO, 'Post cadastrado com sucesso...')->flash();
                    Helpers::renderizar('admin/posts/index');
                endif;
            endif;
        endif;

        echo $this->template->renderizar('posts/formulario.html', [
            'post' => $dados,
            'categorias' => (new CategoriaModelo())->busca()->resultado(true)
        ]);
    }

    public function editar(int $id): void
    {
        $post = (new PostModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) :
            if ($this->checarDados($dados)) :
                $post = (new PostModelo())->buscaPorId($id);
                $post->categoria_id = $dados['categoria_id'];
                $post->usuario_id = $this->usuario->id;
                $post->titulo = $dados['titulo'];
                $post->slug   = Helpers::slugify($dados['titulo']);
                $post->texto  = $dados['texto'];
                $post->status = $dados['status'];

                $post->atualizado_em = date('Y-m-d H:i:s');

                // if (! empty($_FILES['capa'])):
                if ($post->capa && file_exists("uploads/imagens/{$post->capa}")) :
                    unlink("uploads/imagens/{$post->capa}");
                endif;
                $post->capa = $this->capa ?? null;
                // endif;

                if ($post->salvar()) :
                    $this->mensagem->mensagem(SUCESSO, 'Post atualizado com sucesso...')->flash();
                    Helpers::renderizar('admin/posts/index');
                endif;
            endif;
        endif;

        echo $this->template->renderizar('posts/formulario.html', [
            'post' => $post,
            'categorias' => (new CategoriaModelo())->busca()->resultado(true)
        ]);
    }

    public function deletar(int $id)
    {
        $post = (new PostModelo())->buscaPorId($id);
        # testar se o id é um número inteiro e se existe na tabela
        if (is_int($id)) :
            if (!$post) :
                $this->mensagem->mensagem(ATENCAO, 'Id informado não foi encontrado...')->flash();
                Helpers::renderizar('admin/posts/index');
            endif;
        endif;

        if (!$post->apagar("id = {$id}")) :
            $this->mensagem->mensagem(ERRO, $post->erro())->flash();
            Helpers::renderizar('admin/posts/index');
        endif;

        $post->apagar("id = {$id}");

        if ($post->capa && file_exists("uploads/imagens/{$post->capa}")) :
            unlink("uploads/imagens/{$post->capa}");
        endif;

        $this->mensagem->mensagem(SUCESSO, 'Post apagado com sucesso...')->flash();

        Helpers::renderizar('admin/posts/index');
    }

    private function checarDados(array $dados): bool
    {
        // if (! empty($_FILES['capa'])):
        //     $upload = new Upload();
        //     $upload->arquivo($_FILES['capa'], Helpers::slugify($dados['titulo']), 'imagens');

        //     if ($upload->getResultado()):
        //         $this->capa = $upload->getResultado();
        //     else:
        //         $this->mensagem->mensagem(ERRO, $upload->getErro())->flash();
        //         return false;
        //     endif;            
        // endif;

        if (!Helpers::verificarInputs($dados)) :
            $this->mensagem->mensagem(ERRO, 'Os campos são de preenchimento obrigatório...')->flash();
            return false;
        endif;

        if (!empty($_FILES['capa'])) {
            $upload = new Upload($_FILES['capa'], 'pt_BR');
            if ($upload->uploaded) {
                $titulo = $upload->file_new_name_body = Helpers::slugify($dados['titulo']);
                $upload->jpeg_quality  = 90;
                $upload->image_convert = 'jpg';
                $upload->process('uploads/imagens/');

                if ($upload->processed) {
                    $this->capa = $upload->file_dst_name;
                    $upload->file_new_name_body   = $titulo;
                    $upload->image_resize         = true;
                    $upload->image_x              = 540;
                    $upload->image_y              = 304;
                    $upload->jpeg_quality         = 70;
                    $upload->image_convert        = 'jpg';
                    $upload->image_ratio_y        = true;
                    $upload->process('uploads/imagens/thumbs/');
                    $upload->clean();
                } else {
                    $this->mensagem->mensagem(ERRO, $upload->error)->flash();
                    return false;
                }
            }
        }

        return true;
    }
}
