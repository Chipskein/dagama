# [Dagama](https://dagama.herokuapp.com/)

## Descrição

Dagama foi uma rede social desenvolvida em 2021 para a disciplina de Banco de Dados do IFRS coordenada por Rafael Betito.
O projeto dagama inicialmente foi desenvolvido por:

* [Bruno Nascimento](https://github.com/Chipskein)
* [Victor Amaral](https://github.com/VictorAmaral22)
* [Silvio Quintana](https://github.com/SilvioGQ)
* [Adriele Colossi](https://github.com/adrielecolossi)

**Já esta versão se trata de uma versão com outro banco de dados(Mysql),refatorada e com alguns features removidos estes sendo:**
* Selos
* Localização(cidade,estado) para apenas paises

## [Para Testar](https://dagama.herokuapp.com/)

## Caso rode local:
#### ⚠️ É necessário a instalar e configurar módulos do php-redis
#### Instale as dependencias
    composer install
#### Configure as variaveis de ambiente como no .env.example e no credentrials.example.json
#### Por fim inicie o webserver com
      php -S localhost:8080 -t public/
#### ou utilizar o [heroku-cli](https://devcenter.heroku.com/articles/heroku-cli)
      heroku local local-server
## Desenvolvedor Dessa versão
* [Bruno Nascimento](https://github.com/Chipskein)
## Diagrama do Banco
![dagama_proto](https://github.com/Chipskein/dagama/blob/mysql_master/database/dagama.png)
