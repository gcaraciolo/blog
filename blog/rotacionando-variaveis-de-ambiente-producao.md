# Rotacionando variáveis de ambiente em produção

Você já precisou trocar as variáveis de ambiente de um projeto que está rodando em produção? Bem eu já, e as primeiras experiências não foram nada legais!

Irei compartilhar nesse post um passo a passo do que você deve fazer se também precisar rotacionar as chaves do seu app no ambiente de produção.

Variáveis de ambiente são utilizadas geralmente para armazenar senhas e/ou informações secretas de um aplicativo para acessar outros serviços. Por exemplo, se sua aplicação tem login pelo Google, é necessário informar uma chave secreta para que o Google identifique sua aplicação a cada solicitação de login.

**Armazenar grupo de chaves em uma única variável**

Existem variáveis que precisam ser utilizadas em conjunto. Por exemplo, aplicações que se comunicam com Simple Queue Service, serviço de fila da AWS, e utiliza um usuário para acessar esse serviço, precisa informar as credenciais Access Key e Secret Key desse usuário.

Na rotação dessas chaves, se você trocar a chave Access Key e esquecer ou errar a Secret Key, a aplicação irá ficar instável na parte de comunicação por fila.

Para evitar esse tipo de erro, utilize uma única variável para armazenar ambas as chaves e na sua aplicação crie uma lógica para separar as chaves em variáveis diferentes. Vamos a um exemplo. Irei utilizar PHP e o Laravel como framework, mas essa técnica se aplica a qualquer framework, inclusive nenhum.

Arquivo config/cache.php
```php
<?php
 
return [
     'dynamodb' => [
           'driver' => 'dynamodb',
           'key' => env('AWS_ACCESS_KEY_ID'),
           'secret' => env('AWS_SECRET_ACCESS_KEY'),
           'region' => 'us-west-2',
           'table' =>  'cache',
           'endpoint' => env('DYNAMODB_ENDPOINT'),
       ]
]
```
Esse é o típico cenário onde um erro na chave `AWS_ACCESS_KEY_ID` pode derrubar a aplicação. Por isso no lugar de tentar acessar a variável direto com função env() individualmente, utilize apenas uma variável de ambiente para armazenar os dois valores e na sua aplicação faça lógica para separar essas variáveis.

Usando esse mesmo exemplo de chaves da aws, podemos adicionar um caractere ponto-e-vírgula entre as chaves e na hora de recuperar ficaria assim:
```php
<?php
 
$dynamoUserCredentials = explode(';', env('AWS_DYNAMODB_USER_CREDENTIALS'));
$accessId = $dynamoUserCredentials[0];
$secretKey = $dynamoUserCredentials[1];
 
return [
   'dynamodb' => [
       'driver' => 'dynamodb',
       'key' => $accessId,
       'secret' => $secretKey,
       'region' => 'us-west-2',
       'table' =>  'cache',
       'endpoint' => env('DYNAMODB_ENDPOINT'),
   ],
];
```

**Versionamento das chaves com fallback**

Para realizar uma mudança das variáveis de ambiente sem problemas, sempre coloque um número nas versões das variáveis de ambiente e crie um mecanismo de fallback para a versão anterior. Acho que fica melhor falar com um código de exemplo.

```php
$dynamoUserCredentials = explode(';', env('AWS_DYNAMODB_USER_CREDENTIALS.v1'));
```

No exemplo acima, foi adicionado o sufixo v1 na variável. Para criar o fallback, a função env aceita um segundo argumento para ser utilizado caso o primeiro seja NULL. Ficaria assim:

```php
$dynamoUserCredentials = explode(';', env('AWS_DYNAMODB_USER_CREDENTIALS.v2', env('AWS_DYNAMODB_USER_CREDENTIALS.v1')));
```

Assim, se a chave v2 ainda não estiver acessível em produção, a chave v1 será utilizada no lugar.

Com isso, podemos fazer a troca dessa chave uma máquina por vez em produção. Assim que a aws informar que a chave v1 não está mais sendo utilizada por alguns dias, podemos remover ela como opção de fallback.

Bem, é isso. Espero que essas duas dicas tenham ajudado :D
