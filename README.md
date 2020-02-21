# cliHelper

Execute comandos pelo bash para facilitar a criação de Models no formato do Framework MVC

O cliHelper vai, com base nas informações de Banco de Dados, criar uma Classe Model completa para efetaur as principais operações de persistencia em banco de dados.
- INSERT
- UPDATE
- DELETE
- SELECT (ID)
- SELECT (ALL)

### Instalação
```
cp config.ini-dist config.ini
```

Depois, basta configurar todos os parametros do arquivo `config.ini` de acordo com o seu projeto.

# TODO
- General
    - Subistituir os echos pelo método `$this->myecho` para evitar problemas de encoding
- Model
    - Complementar PHPDocs de acordo com o arquivo de configuração
    - Criar o método que vai enviar o arquivo gerado para a pasta do projeto
- Controller
    - Tudo