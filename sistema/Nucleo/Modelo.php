<?php

# namespace
namespace sistema\Nucleo;

use sistema\Nucleo\Conexao;
use sistema\Nucleo\Mensagem;

/**
 * Classe responsável pelas operações de banco de dados e que 
 * servirá às classes de modelos de tabelas do banco de dados
 */
abstract class Modelo
{
    # atributos protegidos (esta classe será herdada)
    protected $tabela;
    protected $dados;
    protected $query;
    protected $termos;
    protected $parametros;
    protected $colunas;
    protected $erro;
    protected $ordem;
    protected $limite;
    protected $offset;
    protected $mensagem;

    /**
     * Construtor da classe (recebe o nome da tabela)
     * @param string $tabela Nome da tabela
     */
    public function __construct(string $tabela)
    {
        $this->tabela = $tabela;

        $this->mensagem = new Mensagem();
    }       

    /**
     * __set() é executado ao escrever dados em atributos inacessíveis.
     * @param type $nome
     * @param type $valor
     */
    public function __set($nome, $valor)
    {
        if (empty($this->dados)) {
            $this->dados = new \stdClass();
        }
        $this->dados->$nome = $valor;
    }    

    /**
     * __get é ativado sempre que tentar acessar um atributo que não existe ou está inacessivel
     * @param type $nome
     * @return type
     */
    public function __get($nome)
    {
        return $this->dados->$nome ?? null;
    }

    /**
     * __isset() é disparado ao chamar a função isset() ou empty() em atributos inacessíveis.
     * @param type $nome
     * @return type
     */
    public function __isset($nome)
    {
        return isset($this->dados->$nome);
    }

    /**
     * Dados
     * @return object|null
     */
    public function dados(): ?object
    {
        return $this->dados;
    }

    /**
     * Método responsável pela ordem de apresentação dos registros
     * @param string $ordem Ordem dos registros da tabela
     * @return objeto classe
     */
    public function ordem(string $ordem)
    {
        $this->ordem = " ORDER BY {$ordem}";
        return $this;
    }
    /**
     * Método responsável pelo limite de apresentação dos registros
     * @param string $limite Limite dos registros da tabela
     * @return objeto classe
     */
    public function limite(?string $limite)
    {
        // $this->limite = ($limite ? " LIMIT {$limite}" : "");
        $this->limite = " LIMIT {$limite} ";
        return $this;
    }
    /**
     * Método responsável pelo offset de apresentação dos registros
     * @param string $offset Offset dos registros da tabela
     * @return objeto classe
     */
    public function offset(?string $offset)
    {
        // $this->offset = ($offset ? " OFFSET {$offset}" : "");
        $this->offset = " OFFSET {$offset} ";
        return $this;
    }
   
    /**
     * Erros
     * @return \PDOException|null
     */
    public function erro(): ?\PDOException
    {
        return $this->erro;
    }

    /**
     * Mensagens
     * @return Mensagem|null
     */
    public function mensagem(): ?Mensagem
    {
        return $this->mensagem;
    }    

    /**
     * Método responsável por fazer uma consulta na tabela
     * @param ?string $termos Condições da consulta (pode ser nulo)
     * @param ?string $parametros Parâmetros da consulta (pode ser nulo)
     * @param string $colunas Colunas da tabela que serão visualizadas
     */
    public function busca(?string $termos = null, ?string $parametros = null, string $colunas = "*")
    {      
        if ($termos) :
            $this->query = "SELECT {$colunas} FROM " . $this->tabela . " WHERE {$termos}";
            if (isset($parametros)):
                parse_str($parametros, $this->parametros);
            endif;
            return $this;
        endif;

        $this->query = "SELECT {$colunas} FROM " . $this->tabela;
        return $this;
    }

     /**
     * 
     */
    public function buscaPorId(int $id)
    {
        $busca = $this->busca("id = {$id}");
        return $busca->resultado();
    }

     /**
     * 
     */
    public function buscaPorSlug(string $slug)
    {
        $busca = $this->busca("slug = :s", "s={$slug}");
        return $busca->resultado();
    }

    /**
     * Método que recebe os dados da consulta da tabela do banco de dados
     * @param bool $todos Define a quantidade de registros do resultado da consulta
     */
    public function resultado(bool $todos = false): mixed
    {
        try {
            $stmt = Conexao::getInstancia()->prepare($this->query . $this->ordem . $this->limite . $this->offset);
            $stmt->execute($this->parametros);

            if (! $stmt->rowCount()) :
                return null;
            endif;

            if ($todos) :
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            endif;

            // return $stmt->fetch();
            return $stmt->fetchObject(static::class);

        } catch (\PDOException $e) {
            $this->erro = $e;
            return null;
        }
    }

    /**
     * 
     */
    protected function cadastrar(array $dados)
    {
        try {

            $colunas = implode(', ', array_keys($dados));
            $valores = ':' . implode(', :', array_keys($dados));

            $query = "INSERT INTO " . $this->tabela . " ({$colunas}) VALUES ({$valores})";

            $stmt = Conexao::getInstancia()->prepare($query);
            $stmt->execute($this->filtro($dados));

            return Conexao::getInstancia()->lastInsertId();
            
        } catch (\PDOException $e) {
            $this->erro = $e;
            return null;
        }
    }

    /**
     * 
     */
    protected function atualizar(array $dados, string $termos)
    {
        try {

            $valores = [];
            foreach ($dados as $chave => $valor) {
                $valores[] = "{$chave} = :{$chave}";
            }

            $valores = implode(', ', $valores);

            $query = "UPDATE " . $this->tabela . " SET {$valores} WHERE {$termos}";

            $stmt = Conexao::getInstancia()->prepare($query);
            $stmt->execute($this->filtro($dados));

            return ($stmt->rowCount() ?? 1);
            
        } catch (\PDOException $e) {
            $this->erro = $e;
            return null;
        }
    }

    public function apagar(string $termos)
    {
        try {

            $query = "DELETE FROM " . $this->tabela . " WHERE {$termos}";

            $stmt = Conexao::getInstancia()->prepare($query);
            $stmt->execute();
            return true;

        } catch (\PDOException $e) {
            $this->erro = $e->getMessage();
            return null;
        }
    }

    /**
     * 
     */
    protected function armazenar()
    {
        $dados = (array) $this->dados;
        return $dados;
    }
    
    public function salvar()
    {
        // CADASTRAR
        if (empty($this->id)):
            $id = $this->cadastrar($this->armazenar());

            if ($this->erro):
                $this->mensagem->mensagem(ERRO, 'Ocorreu um erro ao salvar o registro...');
                return false;
            endif;
        endif;

        // ATUALIZAR
        if (! empty($this->id)):
            $id = $this->id;
            $this->atualizar($this->armazenar(), "id = {$id}");

            if ($this->erro):
                $this->mensagem->mensagem(ERRO, 'Ocorreu um erro ao salvar o registro...');
                return false;
            endif;
        endif;

        // $this->dados = $this->buscaPorId($id)->dados();
        return true;
    }

    /**
     * 
     */
    private function filtro(array $dados): array
    {
        $filtro = [];
        foreach ($dados as $chave => $valor) {
            $filtro[$chave] = (is_null($valor) ? null : filter_var($valor, FILTER_DEFAULT));
        }
        return $filtro;
    }

    /**
     * 
     */
    public function totalCond(?string $termo = null): int
    {
        $termo = ($termo ? " WHERE $termo " : '');
        $query = "SELECT id FROM ".$this->tabela." $termo";
        $stmt = Conexao::getInstancia()->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Retorna o total de registros
     * @return int
     */
    public function total(): int
    {
        $stmt = Conexao::getInstancia()->prepare($this->query);
        $stmt->execute($this->parametros);
        return $stmt->rowCount();
    }

    /**
     * 
     */
    public function totalSlug(): int
    {
        $stmt = Conexao::getInstancia()->prepare($this->query);
        $stmt->execute($this->parametros);
        return $stmt->rowCount();
    }

    /**
     * Método responsável por retornar o último id da tabela
     */
    public function ultimoId(): int
    {
        return Conexao::getInstancia()->query("SELECT MAX(id) AS maximo FROM {$this->tabela}")->fetch()->maximo + 1;
    }

    /**
     * Método para gerar um slug
     */
    public function slug(): void
    {
        # buscar slug que seja igual e com id diferente
        $checarSlug = $this->busca("slug = :s AND id != :id", "s={$this->slug}&id={$this->id}");
        if ($checarSlug->totalSlug()):
            $this->slug = "{$this->slug}-{$this->ultimoId()}";
        endif;
    }
}
