<?php

namespace Cli\Helper\Model;

use Cli\Helper\Utils;
use Exception;

class Model
{
    use Utils;

    /** @var array */
    private $tables;

    /** @var array */
    private $fields;

    /**
     * @var string
     */
    private $strClassAtributes = '';
    /**
     * @var string
     */
    private $strClassListar = '';
    /**
     * @var string
     */
    private $strClassCarregarResult = '';
    /**
     * @var string
     */
    private $strClassInserir = '';
    /**
     * @var string
     */
    private $strClassAtualizar = '';
    /**
     * @var string
     */
    private $className = '';
    /**
     * @var string
     */
    private $tablename = '';

    public function __construct()
    {
        $this->loadIniSettings();
        $this->testConnection();
        $this->run();
    }

    /**
     * @throws Exception
     */
    private function run()
    {
        echo PHP_EOL . PHP_EOL;
        echo "Deseja ver a lista de tabelas?" . PHP_EOL;
        if ($this->condicional()) {
            $this->listaTabelas();
        }
        $this->tablename = $this->selecionarTabela();
        $this->className = $this->dashesToCamelCase($this->tablename);
        $this->setFields();
        $this->tratFields();
        $this->fillTemplate();

        echo PHP_EOL;

        $this->myecho('Arquivo gerado com sucesso!', true);
//        $modelPath = '/src/app/models/';
//        system('cp exit/' . $this->className . '.php ' . $this->settings['PROJECT_HOME'] . $modelPath . $this->className . '.php');
//        $this->myecho("Arquivo copiado para: " . $this->settings['PROJECT_HOME'] . $modelPath . $className . '.php');
//        echo PHP_EOL;
//        $this->menu();

    }

    private function selecionarTabela()
    {
        echo PHP_EOL;
        $this->myecho("Digite o nome da tabela que você quer usar em seu Model: ");
        $tablename = readline();
        if ( ! in_array($tablename, $this->tables)) {
            echo PHP_EOL . " Tabela não encontrada! " . PHP_EOL;
            $this->selecionarTabela();
        }
        return $tablename;
    }

    private function setFields()
    {
        $fields = [];
        $tableDesc = $this->query('desc ' . $this->tablename);
        foreach ($tableDesc as $k => $campo) {
            $fields[$k]['field'] = $campo['Field'];
            $fields[$k]['type'] = $this->typeMapper[explode('(', explode(' ', $campo['Type'])[0])[0]];
            $fields[$k]['atribute'] = $this->convert2CamelCase($campo['Field']);
        }

        $this->fields = $fields;
    }

    private function tratFields()
    {
        foreach ($this->fields as $field) {
            if ( ! is_null($field) && end($this->fields)['field'] == $field['field'] ) {
                $gomma = '';
            } else {
                $gomma = ',';
            }
            $this->strClassAtributes .= "    /** @var " . $field['type'] . " */"  . PHP_EOL;
            $this->strClassAtributes .= "    public $" . $field['atribute'] . ";" . PHP_EOL;
            $this->strClassAtributes .= PHP_EOL;
            $this->strClassListar .= "                    " . $field['field'] . $gomma . PHP_EOL;
            $this->strClassCarregarResult .= '        $this->' . $field['atribute'] . ' = $row[\'' . $field['field'] . '\'];' . PHP_EOL;
            if ($field['type'] == 'int') {
                $this->strClassInserir .= '                    {$this->' . $field['atribute'] . '}' . $gomma . PHP_EOL;
            } else {
                $this->strClassInserir .= '                    \'{$this->' . $field['atribute'] . '}\'' . $gomma . PHP_EOL;
            }
            if ($field['type'] == 'int') {
                $this->strClassAtualizar .= '                    ' . $field['field'] . ' = {$this->' . $field['atribute'] . '}' . $gomma . PHP_EOL;
            } else {
                $this->strClassAtualizar .= '                    ' . $field['field'] . ' = \'{$this->' . $field['atribute']  . '}\'' . $gomma . PHP_EOL;
            }
        }
    }

    private function fillTemplate()
    {
        $template = file_get_contents(__DIR__ . '/ModelTemplate');
        $template = str_replace('<<CLASSNAME>>', $this->className, $template);
        $template = str_replace('<<ATRIUTES>>', $this->strClassAtributes, $template);
        $template = str_replace('<<SELECT_FIELDS>>', $this->strClassListar, $template);
        $template = str_replace('<<TABLE_NAME>>', $this->tablename, $template);
        $template = str_replace('<<PRIMARY_FIELD>>', $this->fields[0]['field'], $template);
        $template = str_replace('<<PRIMARY_ATRIBUTE>>', $this->fields[0]['atribute'], $template);
        $template = str_replace('<<LOAD_RESULT>>', $this->strClassCarregarResult, $template);
        $template = str_replace('<<INSERT_FIELDS>>', $this->strClassInserir, $template);
        $template = str_replace('<<UPDATE_FIELDS>>', $this->strClassAtualizar, $template);

        file_put_contents(__DIR__ . '/../../exit/' . $this->className . '.php', $template);
    }
}
