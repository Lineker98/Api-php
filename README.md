# <font color=#FF003E> API para operações bancárias </font>

<br>

A API desenvolvida em PHP possui como objetivo a realização de operações bancárias como saques, depósitos, retiradas de saldo e extratos 
em diferentes moedas, realizando dos valores das moedas através da [API](https://dadosabertos.bcb.gov.br/dataset/taxas-de-cambio-todos-os-boletins-diarios/resource/9d07b9dc-c2bc-47ca-af92-10b18bcd0d69) fornecida pelo Banco Central.

Para maiores informações sobre os requisitos do desafio, acesse o [link](https://github.com/Lineker98/Api-php/blob/main/informa%C3%A7%C3%B5es/desafio_programador.pdf)


## 📋 Pré-requisitos para executar a aplicação

 - Instalação do [composer 2.1.8](https://getcomposer.org/download/)
 - Instalação do [XAMPP](https://www.apachefriends.org/pt_br/index.html)

Realizados os passos anteriores, para instalação das dependências, com o terminal aberto na pasta raiz digite o seguinte comando:

```
php composer.phar install
```

Posteriormente, abra o painel de controle do XAPP e ligue o servidor apache e o banco de dados mysql, como ilustrado abaixo.

![img](https://github.com/Lineker98/Api-php/blob/main/informa%C3%A7%C3%B5es/xampp.png)

Ainda na pasta raiz do projeto, digite o seguinte comando para subir o servidor local da aplicação:

```
php artisan serve
```

Para realização dos testes, digite o comando:

```
 ./vendor/bin/phpunit tests

```



