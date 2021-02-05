# Configurações para ambiente de desenvolvimento Laravel com XDebug no VSCode (opção de rodar com nginx)

## Instalar o xdebug
Siga os passos do wizard do xdebug. https://xdebug.org/wizard.
Adicione as configurações abaixo.

Configuração php.ini para instalar o XDebug
```
[xdebug]
zend_extension = /usr/local/lib/php/pecl/20180731/xdebug.so
xdebug.idekey = vscode
xdebug.start_with_request = yes
xdebug.client_port = 9003
```

Configuração nginx para quem usa domínio customizado no ambiente de desenvolvimento. Irá realizar o proxy reverso da URI: `http://api.meuprojeto.local` para `http://127.0.0.1:8000` que é onde o script `php artisan serve` está escultado.
```
server {
    listen 80;
    listen [::]:80;
    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    ssl_certificate /usr/local/etc/nginx/ssl/nginx-selfsigned.crt;
    ssl_certificate_key /usr/local/etc/nginx/ssl/nginx-selfsigned.key;

    index index.php index.html;
    server_name http://api.meuprojeto.local;
    root /usr/local/var/www/meuprojeto/public;

    location / {
        proxy_pass http://127.0.0.1:8000;
    }
}
```


Configuração do vscode para que o XDebug se conecte com o Laravel e também inicie o processo.

.vscode/launch.json
```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Laravel XDebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "runtimeExecutable": "/usr/local/opt/php@7.3/bin/php",
            "program": "${workspaceRoot}/artisan",
            "args": ["serve"],
            "env": {
                "XDEBUG_MODE": "debug"
            }
        }
    ]
}
```
