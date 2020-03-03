<?php

namespace Cli\Helper;

use Exception;
use mysqli;

trait Utils
{
    /** @var array */
    private $typeMapper
        = [
            'tinyint'    => 'int',
            'smallint'   => 'int',
            'mediumint'  => 'int',
            'int'        => 'int',
            'bigint'     => 'int',
            'decimal'    => 'double',
            'float'      => 'double',
            'double'     => 'double',
            'bit'        => 'int',
            'char'       => 'string',
            'varchar'    => 'string',
            'binary'     => 'string',
            'varbinary'  => 'string',
            'tinyblob'   => 'string',
            'blob'       => 'string',
            'mediumblob' => 'string',
            'longblob'   => 'string',
            'tinytext'   => 'string',
            'text'       => 'string',
            'mediumtext' => 'string',
            'longtext'   => 'string',
            'enum'       => 'string',
            'set'        => 'string',
            'date'       => 'string',
            'time'       => 'string',
            'datetime'   => 'string',
            'timestamp'  => 'string',
            'yeara'      => 'string'
        ];

    /** @var array */
    private $settings;

    /**
     * Testa a conexãoo com o bando de dados e carrega todas as tabelas do mesmo para a memória
     *
     * @throws Exception
     */
    protected function testConnection()
    {
        $result = $this->query('show tables;');
        if (count($result) == 0) {
            throw new Exception("N�o existem tabelas neste banco de dados");
        }

        foreach ($result as $table) {
            $this->tables[] = $table['Tables_in_unilever_site'];
        }
    }

    /**
     * @param string $sql
     *
     * @return array
     * @throws Exception
     */
    private function query($sql)
    {
        $hostname = $this->settings['DB_HOSTNAME'];
        $datanase = $this->settings['DB_DATABASE'];
        $username = $this->settings['DB_USERNAME'];
        $passowrd = $this->settings['DB_PASSWORD'];
        $hostport = $this->settings['DB_HOSTPORT'];

        $mysqli = new mysqli($hostname, $username, $passowrd, $datanase,
            $hostport);
        if ($mysqli->connect_errno) {
            throw new Exception("Erro ao se conectar com o Banco de Dados");
        }

        $stmt = $mysqli->query($sql);
        $retorno = [];
        while ($row = $stmt->fetch_assoc()) {
            $retorno[] = $row;
        }
        $mysqli->close();
        return $retorno;
    }

    /**
     * Carrega o arquivo de configuração na memória
     *
     * @throws Exception
     */
    protected function loadIniSettings()
    {
        if ( ! file_exists(__DIR__ . '/../config.ini')) {
            throw new Exception("Erro ao carregar as configuracoes. Arquivo de configuracao nao encontrado.");
        }
        $this->settings = parse_ini_file('config.ini');
    }

    /**
     * Imprime o Logo da Aplicação
     */
    protected function printLogo()
    {
        $logo = file_get_contents(__DIR__ . '/../logo');
        echo $logo;
        $this->myecho("Versão " . self::VERSAO);
    }

    /**
     * Imprime na tela convertendo a mensagem para UTF8
     *
     * @param      $buffer
     * @param bool $newline
     */
    private function myecho($buffer, $newline = false, $utf8 = true)
    {
        if ( ! $utf8) {
            $buffer = utf8_encode($buffer);
        }
        echo $buffer;
        if ($newline) {
            echo PHP_EOL;
        }
    }

    private function listaTabelas()
    {
        $qtd = count($this->tables);
        $atual = 0;
        for ($i = 1; $i <= 50;) {
            echo $atual . '>' . $this->tables[$atual] . PHP_EOL;
            $atual++;
            $i++;
            if ($atual == $qtd) {
                return true;
            }
            if ($i > 50) {
                echo PHP_EOL . PHP_EOL;
                echo "Listando " . ($atual) . ' de ' . $qtd . PHP_EOL;
                echo "Deseja listar mais 50 tabelas?" . PHP_EOL;
                if ( ! $this->condicional()) {
                    return true;
                }
                echo PHP_EOL . PHP_EOL;
                $i = 0;
            }
        }

        return true;
    }

    /**
     * @param $resposta
     *
     * @return bool
     */
    protected function condicional()
    {
        echo "SIM [y]" . PHP_EOL;
        echo "NÂO [n]" . PHP_EOL;
        $resposta = readline();
        return (in_array($resposta, array('S', 's', 'SIM', 'sim', 'Y', 'y', 'YES', "yes"))) ? true : false;
    }

    /**
     * Converte UpperCamelCase para camelCase
     *
     * @param $value
     *
     * @return mixed
     */
    private function convert2CamelCase($value)
    {
        $value[0] = strtolower($value[0]);
        return $value;
    }

    /**
     * Converte o padrão DB para camelCase
     *
     * @param $string
     *
     * @return string|string[]
     */
    private function dashesToCamelCase($string)
    {
        $str = str_replace('_', '', ucwords($string, '_'));
        return $str;
    }
}
