<?php

/**
 * cliHelper
 * Versão: 1.0
 * Release: 2020-02-20
 * Autor: Mario de Moraes Barros Neto <mario.moraes@i9xp.com.br>
 */

try {
    $arqumentos = (count($argv) > 1) ? $argv : false;
    $main = new Clihelper($arqumentos);
} catch (Exception $e) {
    echo PHP_EOL;
    echo "--------------------------------------------------------------------------------------------------" . PHP_EOL;
    echo " ERRO! " . $e->getMessage() . PHP_EOL;
    echo "--------------------------------------------------------------------------------------------------" . PHP_EOL;
    print_r($e);
}

class Clihelper
{
    const VERSAO = '1.0';

    /** @var array */
    private $arquments;

    /** @var array */
    private $settings;

    /** @var array  */
    private $menu = [
        'makemodel' => 'Cria um novo Model para o Projeto',
        'sair' => 'Sai do cliHelper',
    ];

    /** @var array */
    private $tables;

    /** @var array  */
    private $typeMapper = [
        'tinyint' => 'int',
        'smallint' => 'int',
        'mediumint' => 'int',
        'int' => 'int',
        'bigint' => 'int',
        'decimal' => 'double',
        'float' => 'double',
        'double' => 'double',
        'bit' => 'int',
        'char' => 'string',
        'varchar' => 'string',
        'binary' => 'string',
        'varbinary' => 'string',
        'tinyblob' => 'string',
        'blob' => 'string',
        'mediumblob' => 'string',
        'longblob' => 'string',
        'tinytext' => 'string',
        'text' => 'string',
        'mediumtext' => 'string',
        'longtext' => 'string',
        'enum' => 'string',
        'set' => 'string',
        'date' => 'string',
        'time' => 'string',
        'datetime' => 'string',
        'timestamp' => 'string',
        'yeara' => 'string'
    ];

    public function __construct($args = false)
    {
        $this->arquments = $args;
        $this->start();
    }

    /**
     * Inicia todos os recursos da Helper
     * @throws Exception
     */
    private function start()
    {
        system("clear");
        $this->loadIniSettings();
        $this->testConnection();
        $this->printLogo();
        if (!$this->arquments) {
            $command = $this->menu();
            $this->$command();
        }
    }

    /**
     * Carrega o arquivo de configuração na memória
     * @throws Exception
     */
    private function loadIniSettings()
    {
        if (!file_exists('config.ini')) {
            throw new Exception("Erro ao carregar as configuracoes. Arquivo de configuracao nao encontrado.");
        }
        $this->settings = parse_ini_file('config.ini');
    }

    /**
     * Testa a conexão com o bando de dados e carrega todas as tabelas do mesmo para a memória
     * @throws Exception
     */
    private function testConnection()
    {
        $result = $this->query('show tables;');
        if (count($result) == 0) {
            throw new Exception("Não existem tabelas neste bando de dados");
        }

        foreach ($result as $table) {
            $this->tables[] = $table['Tables_in_unilever_site'];
        }
    }

    /**
     * Apresenta o menu para o usuário e retorna a opção escolhida pelo mesmo
     * @return int|string
     */
    private function menu()
    {
        echo PHP_EOL . PHP_EOL;
        echo "Lista de Comandos para o cliHelper" . PHP_EOL . PHP_EOL;
        foreach ($this->menu as $command => $description) {
            echo $command . " > " . $description . PHP_EOL;
        }
        echo PHP_EOL . "Digite o comando desejado: ";
        $command = readline();
        if (array_key_exists($command, $this->menu)) {
            echo PHP_EOL;
            return $command;
        }
        echo PHP_EOL . "Erro! - O comando digitado nao e valido";
        return $this->menu();
    }

    /**
     * Comando makemodel
     * @throws Exception
     */
    private function makemodel()
    {
        $this->myecho("Digite o nome da tabela que você quer usar em seu Model: ");
        $tablename = readline();
        $className = $this->dashesToCamelCase($tablename);

        if (!in_array($tablename, $this->tables)) {
            throw new Exception("Tabela não encontrada");
        }

        $tableDesc = $this->query('desc ' . $tablename);

        $fields = [];

        foreach ($tableDesc as $k => $campo) {
            $fields[$k]['field'] = $campo['Field'];
            $fields[$k]['type'] = $this->typeMapper[explode('(', explode(' ', $campo['Type'])[0])[0]];
            $fields[$k]['atribute'] = $this->convert2CamelCase($campo['Field']);
        }

        $strClassAtributes = '';
        $strClassListar = '';
        $strClassCarregarResult = '';
        $strClassInserir = '';
        $strClassAtualizar = '';

        foreach ($fields as $field) {
            if (end($fields)['field'] == $field['field']) {
                $gomma = '';
            } else {
                $gomma = ',';
            }

            $strClassAtributes .= "    /** @var " . $field['type'] . " */" . PHP_EOL;
            $strClassAtributes .= "    public $" . $field['atribute'] . ";" . PHP_EOL;
            $strClassAtributes .= PHP_EOL;
            $strClassListar .= "                    " . $field['field'] . $gomma . PHP_EOL;

            $strClassCarregarResult .= '        $this->' . $field['atribute'] . ' = $row[\'' . $field['field'] . '\'];' . PHP_EOL;

            if ($field['type'] == 'int') {
                $strClassInserir .= '                    {$this->' . $field['atribute'] . '}' . $gomma . PHP_EOL;
            } else {
                $strClassInserir .= '                    \'{$this->' . $field['atribute'] . '}\'' . $gomma . PHP_EOL;
            }

            if ($field['type'] == 'int') {
                $strClassAtualizar .= '                    ' . $field['field'] . ' = {$this->' . $field['atribute'] . '}' . $gomma . PHP_EOL;
            } else {
                $strClassAtualizar .= '                    ' . $field['field'] . ' = \'{$this->' . $field['atribute'] . '}\'' . $gomma . PHP_EOL;
            }
        }

        $template = file_get_contents('templates/Model');
        $template = str_replace('<<CLASSNAME>>', $className, $template);
        $template = str_replace('<<ATRIUTES>>', $strClassAtributes, $template);
        $template = str_replace('<<SELECT_FIELDS>>', $strClassListar, $template);
        $template = str_replace('<<TABLE_NAME>>', $tablename, $template);

        $template = str_replace('<<PRIMARY_FIELD>>', $fields[0]['field'], $template);
        $template = str_replace('<<PRIMARY_ATRIBUTE>>', $fields[0]['atribute'], $template);
        $template = str_replace('<<LOAD_RESULT>>', $strClassCarregarResult, $template);
        $template = str_replace('<<INSERT_FIELDS>>', $strClassInserir, $template);
        $template = str_replace('<<UPDATE_FIELDS>>', $strClassAtualizar, $template);

        file_put_contents('exit/' . $className . '.php', $template);


        exit;
        $this->start();
    }

    /**
     * Comando sair
     */
    private function sair()
    {
        echo PHP_EOL . "Obrigado!";
        exit;
    }

    /**
     * Converte UpperCamelCase para camelCase
     * @param $value
     * @return mixed
     */
    private function convert2CamelCase($value)
    {
        $value[0] = strtolower($value[0]);
        return $value;
    }

    /**
     * Converte o padrão DB para camelCase
     * @param $string
     * @return string|string[]
     */
    private function dashesToCamelCase($string)
    {
        $str = str_replace('_', '', ucwords($string, '_'));
        return $str;
    }

    /**
     * Imprime o Logo da Aplicação
     */
    private function printLogo()
    {
        $logo = file_get_contents('templates/logo');
        echo $logo;
        $this->myecho("Versão " . self::VERSAO);
    }

    /**
     * @param string $sql
     * @return array
     */
    private function query($sql)
    {
        $hostname = $this->settings['DB_HOSTNAME'];
        $datanase = $this->settings['DB_DATABASE'];
        $username = $this->settings['DB_USERNAME'];
        $passowrd = $this->settings['DB_PASSWORD'];
        $hostport = $this->settings['DB_HOSTPORT'];

        $mysqli = new mysqli($hostname, $username, $passowrd, $datanase, $hostport);
//        if ($mysqli->connect_errno) {
//            throw new Exception("Erro ao se conectar com o Bando de Dados");
//        }

        $stmt = $mysqli->query($sql);
        $retorno = [];
        while ($row = $stmt->fetch_assoc()) {
            $retorno[] = $row;
        }
        $mysqli->close();
        return $retorno;
    }

    /**
     * Imprime na tela convertendo a mensagem para UTF8
     * @param $buffer
     * @param bool $newline
     */
    private function myecho($buffer, $newline = false)
    {
        echo utf8_encode($buffer);
        if ($newline) {
            echo PHP_EOL;
        }
    }
}