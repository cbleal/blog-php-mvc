<?php

namespace sistema\Modelo;

use sistema\Nucleo\Modelo;

class PostModelo extends Modelo
{
    public function __construct() 
    {
        parent::__construct('posts_fake');
    }

    public function categoria(): ?CategoriaModelo
    {
        if ($this->categoria_id):
            return (new CategoriaModelo())->buscaPorId($this->categoria_id);
        endif;

        return null;
    }

    public function usuario(): ?UsuarioModelo
    {
        if ($this->usuario_id):
            return (new UsuarioModelo())->buscaPorId($this->usuario_id);
        endif;

        return null;
    }

    public function salvar(): bool
    {
        $this->slug();
        return parent::salvar();
    }



    // public function ler(): array
    // {
    //     $query = 'SELECT * FROM posts ORDER BY id DESC';
    //     $stmt = Conexao::getInstancia()->query($query);
    //     return $stmt->fetchAll();
    // }

    // public function where(string $tabela, string $campo, string $cond, int|string $pesquisa): bool|object
    // {        
    //     $where = " WHERE $campo $cond $pesquisa";
    //     $query = "SELECT * FROM $tabela $where";
    //     $stmt = Conexao::getInstancia()->query($query);
    //     return $stmt->fetch();
    // }

    // public function pesquisa(string $busca): array
    // {
    //     $query = "SELECT * FROM posts WHERE status = 1 AND titulo LIKE '%$busca%' ";
    //     $stmt = Conexao::getInstancia()->query($query);
    //     return $stmt->fetchAll();
    // }

    // public function total(?string $termo = null): int
    // {
    //     $termo = ($termo ? " WHERE $termo " : '');
    //     $query = "SELECT id FROM posts $termo";
    //     $stmt = Conexao::getInstancia()->query($query);
    //     return $stmt->rowCount();
    // }

    // public function armazenar(array $dados): void
    // {
    //     $query = "INSERT INTO posts (categoria_id, titulo, texto, status) VALUES (:categoria_id, :titulo, :texto, :status)";

    //     $stmt = Conexao::getInstancia()->prepare($query);

    //     $stmt->bindParam(':categoria_id', $dados['categoria_id']);
    //     $stmt->bindParam(':titulo', $dados['titulo']);
    //     $stmt->bindParam(':texto', $dados['texto']);
    //     $stmt->bindParam(':status', $dados['status']);

    //     $stmt->execute();
    // }    

    // public function atualizar(array $dados, int $id): void
    // {        
    //     $query = "UPDATE posts SET categoria_id = :categoria_id, titulo = :titulo, texto = :texto, status = :status WHERE id = :id";

    //     $stmt = Conexao::getInstancia()->prepare($query);

    //     $stmt->bindParam(':categoria_id', $dados['categoria_id']);
    //     $stmt->bindParam(':titulo', $dados['titulo']);
    //     $stmt->bindParam(':texto', $dados['texto']);
    //     $stmt->bindParam(':status', $dados['status']);
    //     $stmt->bindParam(':id', $id);

    //     $stmt->execute();
    // }    

    // public function deletar(int $id): void
    // {        
    //     $query = "DELETE FROM posts WHERE id = :id";

    //     $stmt = Conexao::getInstancia()->prepare($query);
       
    //     $stmt->bindParam(':id', $id);

    //     $stmt->execute();
    // }    
    
}