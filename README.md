# [Dagama](https://dagama.herokuapp.com/)
## Descrição
    Rede Social em PHP para disciplina de banco de dados(2021)
## Nota
    Para utilizar todas as funcionalidades do sistema, é necessário instalar o composer.
      composer install
## Requisitos
* [PDF](https://github.com/Chipskein/dagama/blob/main/lista9-projeto.pdf)
## Integrantes do projeto
* [Bruno Nascimento](https://github.com/Chipskein)
* [Victor Amaral](https://github.com/VictorAmaral22)
* [Silvio Quintana](https://github.com/SilvioGQ)
* [Adriele Colossi](https://github.com/adrielecolossi)

# [Design](https://www.figma.com/file/WmCnbvOqMdXhFGvoKSXfjT/dagama.com?node-id=2%3A2)

## Protótipos do banco
    Novo Protótipo(Use o tema escuro no github)
   ![dagama_proto](https://github.com/Chipskein/dagama/blob/main/backend/infra/dagama.png)


Telas
    * Admin
        - 1) CRUD de localidades
            ok
        - 2) CRUD de usuários
            Falta validar, paginação
        - 10) Mostrar quantos usuários receberam mais de C curtidas em uma postagem, em menos de H horas após a postagem,
        no país P nos últimos D dias
            ok
        - 11) Mostrar qual faixa etária mais interagiu às postagens do grupo G nos últimos D dias
            ok
        - 12) Mostrar quais os top T assuntos mais interagidos por mês no país P nos últimos M meses
            ok
        - 13) Mostrar qual assunto permaneceu por mais meses consecutivos entre os top T mais interagidos por mês no país P 
        nos últimos M meses
            ok
        - 15) Desativar temporariamente as contas dos usuários do país P que não possuem qualquer interação há mais de A 
        anos
            ok
        - 16) Atribuir automaticamente um selo de fã, com validade determinada para a semana atual, para os usuários do grupo G conforme a tabela
        ATENÇÃO, AINDA NÃO FEITA
        - 17) Mostrar o gráfico de colunas da quantidade de interações por gênero por faixa etária no país P nos últimos M meses,
        como no exemplo
            ok
    * Feed
        - 3) CRUD de interações
            Falta validações
        - 9) Mostrar um feed com as interações do usuário U, com interações de outros usuários relacionadas a estas interações; 
        as interações de outros usuários que citam o usuário U; as interações dos amigos do usuário U; as postagens dos 
        grupos que o usuário U participa, da interação mais recente para a mais antiga, com paginação e no máximo E 
        elementos por página
            Falta filtrar, pesquisar e validações;
        - 13) Mostrar qual assunto permaneceu por mais meses consecutivos entre os top T mais interagidos por mês no país P 
        nos últimos M meses
            ok
        - 14) Sugerir amigos ao usuário U, considerando que, se U e V não são amigos mas possuem no mínimo A assuntos em 
        comum entre os B assuntos mais comentados por cada um nos últimos M meses, V deve ser sugerido como amigo de U
            Falta validação e testes
    * Mar
        - 7) CRUD de grupos
            Falta ordernar, filtrar, validar e paginação;
    * Portos
        - 3) CRUD de interações
            Falta validações, paginações, excluir interação sua e relacionada as suas; interagir com outras interações; editar as suas interações;
        - 16) Atribuir automaticamente um selo de fã, com validade determinada para a semana atual, para os usuários do grupo 
        G conforme a tabela
    * Perfil
        - 2) CRUD de usuários
            Falta validações;
        - 3) CRUD de interações
            Falta validações, paginações, excluir interação sua e relacionada as suas; interagir com outras interações; editar as suas interações;
        - 6) CRUD de amizades de um usuário 
            Falta validação, paginação, ordernar e filtrar
    * Interações
        - 3) CRUD de interações
            Falta compartilhar, validações, melhorar css
        - 4) CRUD de assuntos de uma interação
            Falta validação
        - 5) CRUD de usuários citados em uma interação
            Falta validação

Observações:
    a) Todas as entradas de dados devem ser validadas tanto no front-end quando no back-end
    b) Todas as telas de seleção/listagem devem possuir filtro pelo conteúdo das células, ordenação crescente/decrescente 
    pelas colunas e paginação
    c) Não deve ser permitida redundância
        ok
    d) Todas as exclusões devem ser do tipo soft delete
        ok
    e) Considere as variáveis (letras maiúsculas) como valores informados pelo professor
        ok
    f) Considere como faixas etárias -18, 18-21, 21-25, 25-30, 30-36, 36-43, 43-51, 51-60 e 60-
    g) Uma interação é uma postagem, comentário, reação ou compartilhamento
        ok
    h) Apenas o dono de um dado pode alterar ou excluir este dado
    i) O dono de uma interação pode excluir interações de outros usuários relacionadas a esta interação
    j) O usuário pode excluir citações suas nas interações de outros usuários
    k) O usuário pode excluir amizades nas quais foi adicionado
        ok
    l) O procedimento de atribuir selo de fã será executado automaticamente às 00:00:00 de cada domingo

Bugs
    - Bug no local do editar
    - Deixar o get dos posts no porto e no perfil igual ao do feed