<?php

namespace sistema\Modelo;

use sistema\Nucleo\Modelo;

class CategoriaModelo extends Modelo
{
    public function __construct() {
        parent::__construct('categorias');
    }

    public function posts($categoria_id): array
    {
        return (new PostModelo())->busca("categoria_id = {$categoria_id} AND status = 1")->resultado(true);
    }

    public function salvar(): bool
    {
        $this->slug();
        return parent::salvar();
    }



    
    // public function ler(): array    {
        
    //     $query = "SELECT * FROM categorias ORDER BY id DESC";
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

    // public function total(?string $termo = null): int
    // {
    //     $termo = ($termo ? " WHERE $termo " : '');
    //     $query = "SELECT id FROM categorias $termo";
    //     $stmt = Conexao::getInstancia()->query($query);
    //     return $stmt->rowCount();
    // }
 
    // public function getPosts(int $id): array
    // {
    //     $query = "SELECT * FROM posts WHERE categoria_id = $id AND status = 1 ORDER BY id DESC";
    //     $stmt = Conexao::getInstancia()->query($query);
    //     return $stmt->fetchAll();
    // }

    // public function armazenar(array $dados): void
    // {
    //     $query = "INSERT INTO categorias (titulo, texto, status) VALUES (?, ?, ?)";
    //     $stmt = Conexao::getInstancia()->prepare($query);
    //     $stmt->execute([$dados['titulo'], $dados['texto'], $dados['status']]);
    // }

    // public function atualizar(array $dados, int $id): void
    // {        
    //     $query = "UPDATE categorias SET titulo = :titulo, texto = :texto, status = :status WHERE id = :id";

    //     $stmt = Conexao::getInstancia()->prepare($query);

    //     $stmt->bindParam(':titulo', $dados['titulo']);
    //     $stmt->bindParam(':texto', $dados['texto']);
    //     $stmt->bindParam(':status', $dados['status']);
    //     $stmt->bindParam(':id', $id);

    //     $stmt->execute();
    // }  

    // public function deletar(int $id): void
    // {        
    //     $query = "DELETE FROM categorias WHERE id = :id";

    //     $stmt = Conexao::getInstancia()->prepare($query);
       
    //     $stmt->bindParam(':id', $id);

    //     $stmt->execute();
    // }   
    
}