<?php

namespace sistema\Nucleo;

use Exception;

class Helpers
{  
    public static function json(string $chave, string $valor): void
    {
        header('Content-Type: application/json');
        $json[$chave] = $valor;
        echo json_encode($json);
        exit();
    }

    public static function renderizar(string $url = null): void
    {
        header('HTTP/1.1 302 Found');
        $local = ($url ? self::url($url) : self::url());
        header("Location: {$local}");
        exit();
    } 
    
    /**
     * Retira qualquer caractere de uma string que não seja número
     * @param string $numero String a ser modificada
     * @return string Nova string modificada
     */
    public static function limparNumero(string $numero): string
    {
        return preg_replace('/[^0-9]/', '', $numero);
    }
    
    /**
     * Transforma uma string no padrão de slug
     * @param string $string String a ser transformada em slug
     * @return string Slug devidamente formatada
     */
    public static function slugify(string $string): string
    {
        $string = preg_replace('/[\t\n]/', ' ', $string);
        $string = preg_replace('/\s{2,}/', ' ', $string);
        $list = array(
            'Š' => 'S',
            'š' => 's',
            'Đ' => 'Dj',
            'đ' => 'dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'Č' => 'C',
            'č' => 'c',
            'Ć' => 'C',
            'ć' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'Ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ŕ' => 'R',
            'ŕ' => 'r',
            '/' => '-',
            ' ' => '-',
            '.' => '-',
            '?' => '',
            '(' => '',
            ')' => '',
        );
    
        $string = strtr($string, $list);
        $string = preg_replace('/-{2,}/', '-', $string);
        $string = strtolower($string);
    
        return $string;
    }
    
    /**
     * Recupera a url de acordo com o ambiente do sistema
     * @param string $url URL Informada
     * @return string URL da aplicação 
     */
    public static function url(string $url = null): string
    { 
        $ambiente = (self::localhost() ? URL_DESENVOLVIMENTO : URL_PRODUCAO);

        // var_dump($ambiente); exit;

        if (isset($url)):
            if (str_starts_with($url, '/')):
                return $ambiente . $url;
            endif;
        endif;
           
        return $ambiente . '/' . $url;
        // return $ambiente . $url;
    }
    
    /**
     * Verifica se o servidor da aplicação é localhost
     * @return bool True ou False
     */
    public static function localhost(): bool
    {
        $servidor = filter_input(INPUT_SERVER, 'SERVER_NAME');
        if ($servidor == 'localhost'):
            return true;
        endif;
        return false;
    }
    
    /**
     * Valida uma URL
     * @param string $url URL a validar
     * @return bool True ou False
     */
    public static function validarUrl(string $url): bool
    {
        if (mb_strlen($url) < 10):
            // return false;
            throw new Exception("URL não pode conter menos de 10 caracteres");
        endif;
    
        if (!str_contains($url, '.')):
            // return false;
            throw new Exception("URL deve possuir um ponto na sua escrita");
        endif;
    
        if (str_contains($url, 'http://') OR str_contains($url, 'https://')):
            return true;          
        endif;
    
        throw new Exception("URL deve começar com http:// ou https:// ");
    }
    
    /**
     * Valida uma URL
     * @param string $url URL a validar
     * @return bool True ou False
     */
    public static function validarUrlComFiltro(string $url): bool
    {
        # filter_var() = filtra uma variável
        return filter_var($url, FILTER_VALIDATE_URL);
    }
    
    /**
     * Valida um endereço de email
     * @param string $email Email a validar
     * @return bool True ou False
     */
    public static function validarEmail(string $email): bool
    {
        # filter_var() = filtra uma variável
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validarString(mixed $string): bool
    {
        return !!preg_match('|^[\pL\s]+$|u', $string);
    }

    public static function validarSenha(string $senha): string
    {
        if (mb_strlen($senha) >= 6 && mb_strlen($senha) <=50):
            return true;
        endif;

        return false;
    }

    public static function gerarSenha(string $senha): string
    {
        return password_hash($senha, PASSWORD_DEFAULT, ['cost' => 10]);
    }

    public static function verificarSenha(string $senha, $hash): bool
    {       
        if (password_verify($senha, $hash)):
            return true;
        endif;

        return false;
    }

    public static function verificarInputs(array $dados): bool
    {
        if (in_array('', $dados)):            
            return false;
        endif;

        return true;
    } 
    
    /**
     * Conta o tempo decorrido de uma data
     * @param string $data Data especificada
     * @return string Data com o tempo decorrido
     */
    public static function contarTempo(string $data): string
    {
        $agora = new \DateTime('now');
        $data_inicial = new \DateTime($data);
    
        $diferenca = $data_inicial->diff($agora);
    
        return $diferenca->d . ' dia(s) ' . $diferenca->m . ' mese(s) ' . $diferenca->y . ' ano(s).';
    }

    public static function calcularTempoDecorrido(string $data): string
    {
        $data_informada = strtotime($data);
        $tempo_atual = time();
        $diferenca = $tempo_atual - $data_informada;
    
        $anos = floor($diferenca / (365 * 24 * 60 * 60));
        $meses = floor($diferenca / (30 * 24 * 60 * 60));
        $dias = floor($diferenca / (24 * 60 * 60));
        $horas = floor($diferenca / (60 * 60));
        $minutos = floor($diferenca / 60);
        $segundos = $diferenca;
    
        if ($anos > 0) {
            return $anos . " ano(s) atrás";
        } elseif ($meses > 0) {
            return $meses . " mês(es) atrás";
        } elseif ($dias > 0) {
            return $dias . " dia(s) atrás";
        } elseif ($horas > 0) {
            return $horas . " hora(s) atrás";
        } elseif ($minutos > 0) {
            return $minutos . " minuto(s) atrás";
        } else {
            return $segundos . " segundo(s) atrás";
        }
    }
    
    
    public static function formataValor(float $valor = null): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
    
    public static function saudacao(): string 
    {
        $hora = date('H');
    
        // if ($hora >=0 AND $hora <=5):
        //     $saudacao = 'boa madrugada';
        // elseif ($hora > 5 AND $hora <=12):
        //     $saudacao = 'bom dia';
        // elseif ($hora >=13 AND $hora <= 18):
        //     $saudacao = 'boa tarde';
        // else:
        //     $saudacao = 'boa noite';
        // endif;
    
        $saudacao = match(true) {
            $hora >=0 AND $hora <=5 => 'boa madrugada',
            $hora >=5 AND $hora <=12 => 'bom dia',
            $hora >= 13 AND $hora <=18 => 'boa tarde',
            default => 'boa noite'
        };
    
        return $saudacao;
    }
    
    /**
     * Resume um texto
     * 
     * @param string $texto Texto a ser resumido
     * @param int $limite Quantidade de caracteres limite
     * @param string $continue Opcional. O que será exibido ao final do resumo
     * @return string Texto resumido
     */
    public static function resumirTexto(string $texto, int $limite, string $continue = '...'): string 
    {
        # strip_tags() = limpa tags HTML de uma string
        $textoLimpo = trim(strip_tags($texto));
        if (mb_strlen($textoLimpo) <= $limite):
            return $textoLimpo;
        endif;
    
        $resumirTexto = mb_substr($textoLimpo, 0, $limite);
        return $resumirTexto . $continue;
    }

    public static function flash(): ?string
    {
        $sessao = new Sessao();

        if ($flash = $sessao->flash()):
            echo $flash;
        endif;

        return null;
    }
}
