# MVC The Right Way

Projeto exemplo para o blog (TODO: url).

## Sobre o projeto
O app irá ajudar desenvolvedores a encontrar repositórios que precisam de contribuidores. O usuário do app (o desenvolvedor) deve fornecer o seu Github para uma análise de perfil do desenvolvedor. 

Com base no repositório do desenvolvedor, deve ser levantando as linguagens de programação que o desenvolvedor já trabalhou.

Os repositórios procurados devem ser em uma das linguagem que o desenvolvedor está acostumado.

## Cenários
Cenário: Desenvolvedor solicita uma monitoramento de repositórios que necessitam de contribuidores

Usuário deve enviar uma request para HTTP POST /api/watcher-repositories

{
    'profile': 'https://github.com/gcaraciolo'
}

Na primeira solicitação do usuário a resposta deverá ser: STATUS 200
Se já houver uma solicitação registrada, a resposta também deverá ser: STATUS 200

O cadastramento do monitoramento deve ser transparente.

Na primeira solicitação, deve ser enviado um email de confirmação para o usuário, informando que
foi iniciado a busca por repositórios e ele será notificado assim que algo for encontrado.
