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

- Model
    - Método Select By Id
    - Método Inserir
    - Método Atualizar
    - Método Excluir
- Controller
