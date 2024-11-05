# Documentação da Arquitetura Docker Swarm

## 1. Visão Geral da Arquitetura
A arquitetura é composta por três componentes principais:
- MySQL Database
- Aplicação PHP
- Load Balancer (Nginx)

```text
                    [Load Balancer - Nginx]
                            |
                            |
        [PHP App 1] -- [PHP App 2] -- [PHP App N]
                            |
                            |
                        [MySQL DB]
```

## 2. Componentes da Arquitetura
### 2.1. MySQL Database
Características:
- Single instance (1 réplica)
- Executa apenas em nós manager
- Dados persistentes via volume
- Credenciais gerenciadas via secrets
- Autenticação nativa do MySQL

### 2.2. Aplicação PHP
Características:
- Múltiplas réplicas (escalável)
- Updates graduais (rolling updates)
- Acesso à secret do MySQL
- Apache integrado
- Conectado à rede app_network

### 2.3. Load Balancer (Nginx)
Características:
- Single instance
- Executa apenas em nós manager
- Configuração via Docker configs
- Porta 80 exposta
- Balanceamento de carga entre instâncias PHP

## 3. Configurações de Rede
### 3.1. Rede Overlay
Características:
- Rede overlay para comunicação entre serviços
- Isolamento de tráfego
- Comunicação segura entre containers

## 4. Gerenciamento de Secrets
### 4.1. Secrets Utilizadas
```text
secrets:
  mysql_root_password:
  mysql_password:
```

## 5. Instruções de Implantação
### 5.1. Preparação do Ambiente
```bash
# Inicializar Swarm
docker swarm init

# Criar rede overlay
docker network create -d overlay app_network

# Criar secrets
echo "SuaSenhaRoot" | docker secret create mysql_root_password -
echo "SuaSenhaUser" | docker secret create mysql_password -
```

## 5.2. Deploy dos Serviços
```bash
# Deploy do MySQL
docker stack deploy -c ./database/stack.yml db_stack

# Deploy da Aplicação PHP
docker stack deploy -c ./app/stack.yml app_stack

# Deploy do Load Balancer
docker stack deploy -c ./lb/stack.yml lb_stack
```

## 6. Comandos de Gerenciamento
### 6.1. Monitoramento
````bash
# Listar serviços
docker service ls

# Verificar logs
docker service logs db_stack_mysql
docker service logs app_stack_php
docker service logs lb_stack_nginx_lb
````

## 6.2. Escalabilidade
````bash
# Escalar serviço PHP
docker service scale app_stack_php=5
````