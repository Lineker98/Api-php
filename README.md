# <font color=#FF003E> API para operações bancárias </font>

<br>

A API desenvolvida em PHP possui como objetivo a realização de operações bancárias como saques, depósitos, retiradas de saldo e extratos 
em diferentes moedas, realizando dos valores das moedas através da [API](https://dadosabertos.bcb.gov.br/dataset/taxas-de-cambio-todos-os-boletins-diarios/resource/9d07b9dc-c2bc-47ca-af92-10b18bcd0d69) fornecida pelo Banco Central.


## 📋 Pré-requisitos para executar a aplicação

 - Instalação do [composer 2.1.8](https://getcomposer.org/download/)
 - Instalação do [XAMPP](https://www.apachefriends.org/pt_br/index.html)

Realizados os passos anteriores, para instalação das dependências, com o terminal aberto na pasta raiz digite o seguinte comando:

```
php composer.phar install
```

Posteriormente, abra o painel de controle do XAPP e ligue o servidor apache e o banco de dados mysql, como ilustrado abaixo.

<div align="center" style="width:100%">
    <img width="30%" src = "./informações/xamp.png">
</div>

Ainda na pasta raiz do projeto, digite o seguinte comando para subir o servidor local da aplicação:

```
php artisan serve
```

Para realização dos testes, digite o comando:

```
 ./vendor/bin/phpunit tests

```



