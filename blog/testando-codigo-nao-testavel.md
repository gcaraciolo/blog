Se você já precisou testar uma regra de negócio e não conseguiu porque o código cliente fazia chamada para funções estáticas?

Acompanha o código abaixo para entender melhor a problematica e uma solução para esse tipo de situação!

```php
<?php

class ClientCommand
{
    public function handle()
    {
        // Analisar o ambiente com objetivo de verificar se o projeto irá rodar sem problemas
        
        $messages = [];

        $phpiniPath = php_ini_loaded_file();
        $messages[] ="Analisando php.ini localizado em $phpiniPath";

        $configs = ini_get_all();

        foreach($configs as $key => $value) {
            if ($key === 'zend.assertions') {
                if ($value['local_value'] == 0) {
                  $messages[] = "Altere a configuração 'zend.assertions' para 1";
                }
            }
        }
        
        return $messages;
    }
}

$command = new ClientCommand();
$messages = $command->handle();

foreach ($messages as $message) {
    print_r($message . PHP_EOL);
}
```

**Como criar um teste automatizado para condição `$value['local_value'] == 0`?**

Bem o problema é que a classe cliente depende de detalhes de implementação na sua regra de negócio!
`Ué.. Como assim?`

Perceba que a classe ClientCommand utiliza as funções nativas `php_ini_loaded_file` `ini_get_all` diretamente.
Esse tipo de prática impossibilita que esse código seja testado pois não temos como mudar o retorno dessas funções
num ambiente de testes.

Certo.. e o que fazer? Testar manualmente?
Não!! Testar manualmente tem um alto custo no longo prazo!

Para contornar esse tipo de problema, sempre devemo criar classes/funções auxiliares que irão servir como um
proxy para as outras funções e injetar essas classes/funções na classe cliente.
`..ã?`

Vamos ver um código para as coisas ficarem mais claras.

```php
<?php

class MachineSettings 
{
    public function inipath()
    {
        return php_ini_loaded_file();
    }

    public function phpini()
    {
        return ini_get_all();
    }
}

class ClientCommand
{
    /**
     * @var MachineSettings
     */
    private $settings;

    public function __construct(MachineSettings $settings)
    {
        $this->settings = $settings;
    }

    public function handle()
    {
        // Analisar o ambiente com objetivo de verificar se o projeto irá rodar sem problemas

        $messages = [];

        $phpiniPath = $this->settings->inipath();
        $messages[] = "Analisando php.ini localizado em $phpiniPath";

        $configs = $this->settings->phpini();

        foreach($configs as $key => $value) {
            if ($key === 'zend.assertions') {
                if ($value['local_value'] == 0) {
                    $messages[] = "Altere a configuração 'zend.assertions' para 1";
                }
            }
        }

        return $messages;
    }
}

$command = new ClientCommand(new MachineSettings());
$command->handle();
```

Pronto! A dependencia está invertida e agora é possível testar a condição `$value['local_value'] == 0` passando um stub para classe ClientCommand no ambiente de teste.

```php
<?php


class InvalidLocalSettingsStub implements MachineSettings 
{
    public function inipath()
    {
        return '/usr/local/etc/php/7.3/php.ini';
    }

    public function phpini()
    {
        return [
            'zend.assertions' => [
                'global_value' => 0,
                'local_value' => 0,
                'access' => 7,
            ]
        ];
    }
}


class ValidLocalSettingsStub implements MachineSettings 
{
    public function inipath()
    {
        return '/usr/local/etc/php/7.3/php.ini';
    }

    public function phpini()
    {
        return [
            'zend.assertions' => [
                'global_value' => 1,
                'local_value' => 1,
                'access' => 7,
            ]
        ];
    }
}

function test_invalid_zend_assertions()
{
    $command = new ClientCommand(new InvalidLocalSettingsStub());
    $messages = $command->handle();

    my_assert(sizeof($messages) > 1, 'Configuração invalida'); 
}

function test_valid_zend_assertions()
{
    $command = new ClientCommand(new ValidLocalSettingsStub());
    $messages = $command->handle();

    my_assert(sizeof($messages) === 1, 'Configuração invalida'); 
}

function my_assert($assertion, $description)
{
    if (!$assertion) {
        throw new AssertionError($description);
    }
}


test_invalid_zend_assertions();
test_valid_zend_assertions();
```

