<?php

/**
 * cliHelper
 * Versão: 1.0
 * Release: 2020-02-20
 * Autor: Mario de Moraes Barros Neto <mario.moraes@i9xp.com.br>
 */

try {
    $arqumentos = (count($argv) > 1) ? $argv : false;
    $main = new clihelper($arqumentos);
} catch (Exception $e) {
    echo PHP_EOL;
    echo "------------------------------------------------------------------------------------------------------------" . PHP_EOL;
    echo " ERRO! " . $e->getMessage() . PHP_EOL;
    echo "------------------------------------------------------------------------------------------------------------" . PHP_EOL;
    print_r($e);
}

class clihelper
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
     * Cria uma classe Model basica de acordo com a tabela
     */
    private function makemodel()
    {
        $this->myecho("Digite o nome da tabela que você quer usar em seu Model: ");
        $tablename = readline();
        if (!in_array($tablename, $this->tables)) {
            throw new Exception("Tabela não encontrada");
        }

        $tableDesc = $this->query('desc ' . $tablename);

        $fields = [];

        foreach ($tableDesc as $k => $campo) {
            $fields[$k]['field'] = $campo['Field'];
            $fields[$k]['type'] = $this->typeMapper[explode('(', explode(' ', $campo['Type'])[0])[0]];
        }

        $strClassAtributes = '';
        $strClassListar = '';
        $strClassInserir = '';
        $strClassAtualizar = '';
        $strClassDeletar = '';

        foreach ($fields as $field) {
            $strClassAtributes .= "    /** @var " . $field['type'] . " */" . PHP_EOL;
            $strClassAtributes .= "    public $" . $field['field'] . ";" . PHP_EOL;
            $strClassAtributes .= PHP_EOL;

            if (end($fields)['field'] == $field['field']) {
                $strClassListar .= "                    " . $field['field'];
            } else {
                $strClassListar .= "                    " . $field['field'] . "," . PHP_EOL;
            }
        }

        $template = file_get_contents('templates/Model');
        $template = str_replace('<<CLASSNAME>>', $tablename, $template);
        $template = str_replace('<<ATRIUTES>>', $strClassAtributes, $template);
        $template = str_replace('<<SELECT_FIELDS>>', $strClassListar, $template);
        $template = str_replace('<<TABLE_NAME>>', $tablename, $template);

        file_put_contents('exit/' . $tablename . '.php', $template);


        exit;
        $this->start();
    }

    private function sair()
    {
        echo PHP_EOL . "Obrigado!";
        exit;
    }

    private function printLogo()
    {
        echo "" . PHP_EOL;
        echo "" . PHP_EOL;
        echo "                    lllllll   iiii  HHHHHHHHH     HHHHHHHHH                   lllllll" . PHP_EOL;
        echo "                    l:::::l  i::::i H:::::::H     H:::::::H                   l:::::l" . PHP_EOL;
        echo "                    l:::::l   iiii  H:::::::H     H:::::::H                   l:::::l" . PHP_EOL;
        echo "                    l:::::l         HH::::::H     H::::::HH                   l:::::l" . PHP_EOL;
        echo "    cccccccccccccccc l::::l iiiiiii   H:::::H     H:::::H      eeeeeeeeeeee    l::::lppppp   ppppppppp       eeeeeeeeeeee    rrrrr   rrrrrrrrr" . PHP_EOL;
        echo "  cc:::::::::::::::c l::::l i:::::i   H:::::H     H:::::H    ee::::::::::::ee  l::::lp::::ppp:::::::::p    ee::::::::::::ee  r::::rrr:::::::::r" . PHP_EOL;
        echo " c:::::::::::::::::c l::::l  i::::i   H::::::HHHHH::::::H   e::::::eeeee:::::eel::::lp:::::::::::::::::p  e::::::eeeee:::::eer:::::::::::::::::r" . PHP_EOL;
        echo ":::::::cccccc:::::c l::::l  i::::i   H:::::::::::::::::H  e::::::e     e:::::el::::lpp::::::ppppp::::::pe::::::e     e:::::err::::::rrrrr::::::r" . PHP_EOL;
        echo "::::::c     ccccccc l::::l  i::::i   H:::::::::::::::::H  e:::::::eeeee::::::el::::l p:::::p     p:::::pe:::::::eeeee::::::e r:::::r     r:::::r" . PHP_EOL;
        echo ":::::c              l::::l  i::::i   H::::::HHHHH::::::H  e:::::::::::::::::e l::::l p:::::p     p:::::pe:::::::::::::::::e  r:::::r     rrrrrrr" . PHP_EOL;
        echo ":::::c              l::::l  i::::i   H:::::H     H:::::H  e::::::eeeeeeeeeee  l::::l p:::::p     p:::::pe::::::eeeeeeeeeee   r:::::r" . PHP_EOL;
        echo "::::::c     ccccccc l::::l  i::::i   H:::::H     H:::::H  e:::::::e           l::::l p:::::p    p::::::pe:::::::e            r:::::r" . PHP_EOL;
        echo ":::::::cccccc:::::cl::::::li::::::iHH::::::H     H::::::HHe::::::::e         l::::::lp:::::ppppp:::::::pe::::::::e           r:::::r" . PHP_EOL;
        echo " c:::::::::::::::::cl::::::li::::::iH:::::::H     H:::::::H e::::::::eeeeeeee l::::::lp::::::::::::::::p  e::::::::eeeeeeee   r:::::r" . PHP_EOL;
        echo "  cc:::::::::::::::cl::::::li::::::iH:::::::H     H:::::::H  ee:::::::::::::e l::::::lp::::::::::::::pp    ee:::::::::::::e   r:::::r" . PHP_EOL;
        echo "    cccccccccccccccclllllllliiiiiiiiHHHHHHHHH     HHHHHHHHH    eeeeeeeeeeeeee llllllllp::::::pppppppp        eeeeeeeeeeeeee   rrrrrrr" . PHP_EOL;
        echo "                                                                                      p:::::p" . PHP_EOL;
        echo "                                                                                      p:::::p" . PHP_EOL;
        echo "                                                                                     p:::::::p" . PHP_EOL;
        echo "                                                                                     p:::::::p" . PHP_EOL;
        echo "                                                                                     p:::::::p" . PHP_EOL;
        echo "                                                                                     ppppppppp" . PHP_EOL;
        echo "                                                                                                                                     ver. " . self::VERSAO . PHP_EOL;
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

    private function myecho($buffer, $newline = false)
    {
        echo utf8_encode($buffer);
        if ($newline) {
            echo PHP_EOL;
        }
    }
}