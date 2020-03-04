<?php

namespace Cli\Helper;

use Cli\Helper\Model\Model;

class Cli
{
    use Utils;

    const VERSAO = '1.0';

    /** @var array */
    private $menu
        = [
            'makemodel' => 'Cria um novo Model para o Projeto',
            'sair'      => 'Sai do cliHelper',
        ];

    /**
     * @var array
     */
    private $arquments;

    public function __construct()
    {
        $argumentos = func_get_args();
        if ( ! empty($argumentos[0])) {
            $this->arquments = current($argumentos);
        }
        $this->start();
    }

    /**
     * Inicia todos os recursos da Helper
     */
    private function start()
    {
        system("clear");
        $this->printLogo();
        $command = (($this->arquments || ! empty($this->arquments))
            && $this->validateMenu($this->arquments[1])) ? $this->arquments[1]
            : $this->menu();
        print_r($command);
        $this->$command();
    }

    protected function validateMenu($command)
    {
        echo PHP_EOL;
        if (array_key_exists($command, $this->menu)) {
            return true;
        } else {
            echo "Erro! - O comando digitado nao e valido";
            return false;
        }
    }

    /**
     * Apresenta o menu para o usuário e retorna a opção escolhida pelo mesmo
     *
     * @return int|string
     */
    protected function menu()
    {
        echo PHP_EOL . PHP_EOL;
        echo "Lista de Comandos para o cliHelper" . PHP_EOL . PHP_EOL;
        foreach ($this->menu as $command => $description) {
            echo $command . " > " . $description . PHP_EOL;
        }
        echo PHP_EOL . "Digite o comando desejado: ";
        $command = readline();
        return ($this->validateMenu($command)) ? $command : $this->menu();
    }

    protected function menuOptions()
    {

    }

    /**
     * Comando sair
     */
    private function sair()
    {
        echo PHP_EOL . "Obrigado!";
        exit;
    }

    private function makemodel()
    {
        new Model();
    }
}
