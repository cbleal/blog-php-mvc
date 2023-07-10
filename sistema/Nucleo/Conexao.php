<?php

namespace sistema\Nucleo;

class Conexao
{
    private static $instancia;

    public static function getInstancia(): \PDO
    {
        try {
            if (empty(self::$instancia)) :
                self::$instancia = new \PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASS, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                ]);    
            endif;
            return self::$instancia;
        } catch (\PDOException $e) {
            die('Erro conexão: ' . $e->getMessage());
        }
    }

    protected function __construct()
    {
        
    }

    private function __clone(): void
    {

    }
}
