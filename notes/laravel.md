- Criar uma aplicação Laravel com Jetstream + livewire + bootstrap. Usar https://github.com/nascent-africa/jetstrap

## Novo projeto Laravel - Dependencias e boas práticas sugeridas
* Utilize uma ferramenta de Análise estática de código. Sugestão vimeo/psalm + psalm/plugin-laravel
* Utilize o phpunit. phpunit/phpunit 
* Utilize strict_types. TODO: achar uma lib que checa se todos os arquivos do projeto estão com `<?php declare(strict_types=1);`

Adicione github actions para rodar os testes e a análise estática de código nos PRs ou commits.
