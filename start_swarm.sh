#!/bin/bash

docker swarm init

# Adicione os outros nós do Swarm com o comando que aparecer após a inicialização

docker stack deploy -c ./database/stack.yml mysql_stack
docker stack ps mysql_stack

docker stack deploy -c ./app/stack.yml app_stack
docker stack ps app_stack

docker stack deploy -c ./lb/stack.yml lb_stack
docker stack ps lb_stack