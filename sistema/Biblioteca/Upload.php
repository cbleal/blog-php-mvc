<?php

namespace sistema\Biblioteca;

class Upload
{
    private ?string $diretorio;
    private ?string $subDiretorio;
    private ?array $arquivo;
    private ?string $nome;
    private ?string $tamanho;

    private ?string $resultado = null;
    private ?string $erro;

    public function __construct(string $diretorio = null) 
    {
        $this->diretorio = $diretorio ?? 'uploads';

        if (!file_exists($this->diretorio) && !is_dir($this->diretorio)):
            mkdir($this->diretorio, 0777);
        endif;
    }

    public function getResultado(): ?string
    {
        return $this->resultado;
    }

    public function getErro(): ?string
    {
        return $this->erro;
    }

    public function arquivo(array $arquivo, string $nome = null, string $subDiretorio = null, int $tamanho = null): void
    {
        # subdiretorio
        $this->subDiretorio = $subDiretorio ?? 'imagens';

        # arquivo enviado pelo formulario
        $this->arquivo = $arquivo;

        # nome do arquivo
        $this->nome = $nome ?? pathinfo($this->arquivo['name'], PATHINFO_FILENAME);

        # validação de extensões permitidas
        $extensao = pathinfo($this->arquivo['name'], PATHINFO_EXTENSION);
        $extensoesPermtidas = ['pdf', 'jpeg', 'jpg', 'png', 'txt'];

        # validação de tipos permitidos
        $tiposPermtidos = [
            'image/jpeg', 
            'application/pdf', 
            'image/png', 
        ];

        # validação de tamanho de arquivo
        $this->tamanho = $tamanho ?? 1;

        if (!in_array($extensao, $extensoesPermtidas)):
            $this->erro = "Extensão não permitida .{$extensao} - Utilize apenas: ." . implode(' .', $extensoesPermtidas);
        elseif (!in_array($this->arquivo['type'], $tiposPermtidos)):
            $this->erro = "Tipo de arquivo não permitido {$this->arquivo['type']} - Utilize apenas:" . implode(' ,', $tiposPermtidos);
        elseif ($this->arquivo['size'] > $this->tamanho * (1024 * 1024)):
            $this->erro = "Tamanho do arquivo {$this->arquivo['size']} excedido - O tamanho permitido é: {$this->tamanho}MB";
        else:
            $this->criarSubDiretorio();
            $this->renomearArquivo();
            $this->moverArquivo();
        endif;
       
    }

    private function criarSubDiretorio(): void
    {
        if (!file_exists($this->diretorio.DIRECTORY_SEPARATOR.$this->subDiretorio) && !is_dir($this->diretorio.DIRECTORY_SEPARATOR.$this->subDiretorio)):
            mkdir($this->diretorio.DIRECTORY_SEPARATOR.$this->subDiretorio, 0777);
        endif;
    }

    private function renomearArquivo(): void
    {
        # recuperar o nome do arquivo com sua extensão
        $extensao = strrchr($this->arquivo['name'], '.');
        $arquivo = $this->nome . $extensao;

        # se o arquivo existir, renomeamos para não causar a substituição
        if (file_exists($this->diretorio.DIRECTORY_SEPARATOR.$this->subDiretorio.DIRECTORY_SEPARATOR.$arquivo)):
            $arquivo = $this->nome . '-' . date('Y-m-d H-i-s') . $extensao;
        endif;

        # se o arquivo não existir, ele manterá o mesmo nome com a extensão
        $this->nome = $arquivo;
    }

    private function moverArquivo(): void
    {
        if (move_uploaded_file($this->arquivo['tmp_name'], $this->diretorio.DIRECTORY_SEPARATOR.$this->subDiretorio.DIRECTORY_SEPARATOR.$this->nome)):
            $this->resultado = $this->nome;
        else:
            $this->resultado = null;
            $this->erro = "Erro ao tentar mover o arquivo...";
        endif;
    }

    private function validarExtensoes(): bool
    {
        $extensao = pathinfo($this->arquivo['name'], PATHINFO_EXTENSION);
        $extensoesPermtidas = ['pdf', 'jpeg', 'jpg', 'png', 'txt'];

        if (!in_array($extensao, $extensoesPermtidas)):
            $this->erro = "Extensão não permitida .{$extensao} - Utilize apenas: ." . implode(' .', $extensoesPermtidas);
            return false;
        endif;
        return true;
    }

    private function validarTipos(): bool
    {
        $tiposPermtidos = [
            'image/jpeg', 
            'application/pdf', 
            'image/png', 
        ];

        if (!in_array($this->arquivo['type'], $tiposPermtidos)):
            $this->erro = "Tipo de arquivo não permitido {$this->arquivo['type']} - Utilize apenas:" . implode(' ,', $tiposPermtidos);
            return false;
        endif;
        return true;
    }

    private function validarTamanho(): ?string
    {               
        if ($this->arquivo['size'] > $this->tamanho * (1024 * 1024)):
            echo 'aqui';
            return "Tamanho do arquivo {$this->arquivo['size']} excedido - O tamanho permitido é: {$this->tamanho}MB";
        endif;
        return null;
    }

}