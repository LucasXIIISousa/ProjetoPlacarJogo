# Projeto A aplicação Placar do jogo

. nesse primeiro commit apos criar o ambiente de criação com Laragon e Laravel<br>
## implementado:
    ✅ Instalação do Laragon
    ✅ Configuração do Laravel
    ✅ Banco de Dados SQLite no .env
    ✅ Tabelas Criadas
    ✅ Criação do Repositório no GitHub
    ✅ Criação do Repositório no GitHub

# Simulação e teste de GET/POST no Insomnia

## Requisição GET

A requisição `GET /campeonatos` retorna a lista de todos os campeonatos cadastrados.

![Requisição GET](./imgs/e82b8b31-8442-4f9f-93ec-ef39f7496b7c.jpg)

## Requisição POST

A requisição `POST /campeonatos` cria um novo campeonato. O corpo da requisição deve conter o nome do campeonato.

![Requisição POST](./imgs/d0bf02a2-8830-469f-b882-64bb9a412db1.jpg)

## Requisição de Simulação

A requisição `POST /campeonatos/{id}/simular` simula um campeonato com base no ID fornecido. O resultado da simulação é retornado na resposta.

![Requisição de Simulação](./imgs/750b226e-5fd5-4592-bcfe-d0319c393232.jpg)

## Como Executar o Projeto

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/seu-projeto.git